import { Component, OnInit, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Location } from '@angular/common';
import { Router } from '@angular/router';

import { RecipeService } from '../../recipe.service';
import { Recipe, Group, RecipeVisibility } from '../../recipe';
import { Tag } from "../../recipe";
import { md5 } from "../../md5";
import * as $AB from 'jquery';
import * as bootstrap from "bootstrap";

@Component({
  selector: 'app-recipe-details',
  templateUrl: './recipe-details.component.html',
  styleUrls: ['./recipe-details.component.css']
})
export class RecipeDetailsComponent implements OnInit {

  @Input() recipe: Recipe;
  numberOfDifficultyStars: number[];
  editing: boolean;
  userAllowedToEdit = false;
  newRecipe: boolean;
  tags: Tag[];
  availableTags: Tag[];
  selectedTag: string;
  userGroups: Group[];
  recipeMultiplier: number;
  isFavourite: boolean;

  constructor(private route: ActivatedRoute,
    private recipeService: RecipeService,
    private location: Location,
    private router: Router) { }

  ngOnInit() {
    this.getRecipe();
    this.recipeService.getGroups().subscribe(groups => {
      this.userGroups = groups;
    })

    var pointer = this;
    $('#visibilityModal').on('shown.bs.modal', function (e) {
      pointer.initializeVisibilityModal();
    })
  }

  getRecipe(): void {



    this.recipe =
      {
        id: null,
        name: '',
        photos: [],
        duration: "",
        username: "",
        difficulty: null,
        servings: null,
        description: '',
        ingredients: [{ id: null, name: "", amount: null, unit: "" }],
        preparation: [""],
        tags: [],
        userId: null,
        globalAuthenticationLevel: 0,
        groupsAuthenticationLevel: []
      };

    const id = +this.route.snapshot.paramMap.get('id');
    if (isNaN(id)) {
      this.recipe.id = null;

      this.editing = true;
      this.newRecipe = true;
    }
    else {
      this.recipeService.getRecipe(id)
        .subscribe(recipe => {
          this.recipe = recipe;
          this.recipeService.getUsernameById(recipe.userId)
            .subscribe(username => { this.recipe.username = username })
          this.numberOfDifficultyStars = Array(+this.recipe.difficulty).fill(1);
          if (!this.recipe.tags)
            this.recipe.tags = [];
          if (!this.recipe.ingredients)
            this.recipe.ingredients = [];
          if (!this.recipe.preparation)
            this.recipe.preparation = [];
          if (!this.recipe.photos)
            this.recipe.photos = [];
          if (this.recipe.servings)
            this.recipeMultiplier = this.recipe.servings;
          else
            this.recipeMultiplier = 1;
          this.recipeService.getTags()
            .subscribe(tags => {
              this.tags = tags;
              this.filterAvailableTags();
              console.log(this.availableTags);
            });

          this.recipeService.getGroupsByRecipe(this.recipe.name).subscribe(authGroups => {
            this.recipe.groupsAuthenticationLevel = authGroups;
          });
          this.userAllowedToEdit = this.isUserAllowedToEdit();
          this.updateFavourite();
        });

      this.newRecipe = false;
      this.editing = false;
    }


  }
  updateFavourite() {
    this.recipeService.isFavourite(this.recipe.id, this.recipeService.usernameSession)
      .subscribe(isFav => { this.isFavourite = isFav; })
  }

  getTagFromId(tagId): string {
    return this.recipeService.searchTagById(tagId, this.tags);
  }

  getTagIconFromId(tagId): string {
    return this.recipeService.searchTagIconById(tagId, this.tags);
  }

  getGroupFromId(groupVisibility) {
    return this.recipeService.searchGroupById(groupVisibility.groupId, this.userGroups);
  }

  getUserNameFromId(id) {
    this.recipeService.getUsernameById(id)
    //.subscribe(username =>
    //return username;
    // )
  }

  initializeVisibilityModal() {
    if (this.recipe.globalAuthenticationLevel) {
      var visibilityCheckbox = (<HTMLInputElement>document.getElementById('visibilityPublic'));
      var editingCheckbox = (<HTMLInputElement>document.getElementById('editingPublic'));
      visibilityCheckbox.checked = false;
      editingCheckbox.checked = false;
      if (this.recipe.globalAuthenticationLevel == 2) {
        visibilityCheckbox.checked = true;
        editingCheckbox.checked = true;
      }
      if (this.recipe.globalAuthenticationLevel == 1) {
        visibilityCheckbox.checked = true;
        editingCheckbox.checked = false;
      }

    }
    if (this.recipe.groupsAuthenticationLevel) {
      for (var i = 0; i < this.recipe.groupsAuthenticationLevel.length; i++) {

        var visibilityCheckbox = (<HTMLInputElement>document.getElementById('visibility' + this.recipe.groupsAuthenticationLevel[i].groupId));
        var editingCheckbox = (<HTMLInputElement>document.getElementById('editing' + this.recipe.groupsAuthenticationLevel[i].groupId));
        if (this.recipe.groupsAuthenticationLevel[i].authenticationLevel == 2) {
          visibilityCheckbox.checked = true;
          editingCheckbox.checked = true;
          continue;
        }
        if (this.recipe.groupsAuthenticationLevel[i].authenticationLevel == 1) {
          visibilityCheckbox.checked = true;
          editingCheckbox.checked = false;
          continue;
        }
        visibilityCheckbox.checked = false;
        editingCheckbox.checked = false;

      }
    }

  }

  setVisibilityCheckboxes(id) {
    if (id == 'visibilityAll') {
      var state = (<HTMLInputElement>document.getElementById('visibilityAll')).checked;

      var visibilityCheckbox = (<HTMLInputElement>document.getElementById('visibilityPublic'));
      var editingCheckbox = (<HTMLInputElement>document.getElementById('editingPublic'));
      visibilityCheckbox.checked = state;
      if (editingCheckbox.checked) {
        visibilityCheckbox.checked = true;
      }

      for (var i = 0; i < this.userGroups.length; i++) {
        var visibilityCheckbox = (<HTMLInputElement>document.getElementById('visibility' + this.userGroups[i].id));
        var editingCheckbox = (<HTMLInputElement>document.getElementById('editing' + this.userGroups[i].id));
        visibilityCheckbox.checked = state;
        if (editingCheckbox.checked) {
          visibilityCheckbox.checked = true;
        }
      }
      return;
    }

    if (id == 'editingAll') {
      var state = (<HTMLInputElement>document.getElementById('editingAll')).checked;
      (<HTMLInputElement>document.getElementById('visibilityAll')).checked = true;

      if (state == true) {
        var visibilityCheckbox = (<HTMLInputElement>document.getElementById('visibilityPublic'));
        visibilityCheckbox.checked = state;
      }
      var editingCheckbox = (<HTMLInputElement>document.getElementById('editingPublic'));
      editingCheckbox.checked = state;

      for (var i = 0; i < this.userGroups.length; i++) {
        if (state == true) {
          var visibilityCheckbox = (<HTMLInputElement>document.getElementById('visibility' + this.userGroups[i].id));
          visibilityCheckbox.checked = state;
        }
        var editingCheckbox = (<HTMLInputElement>document.getElementById('editing' + this.userGroups[i].id));
        editingCheckbox.checked = state;

      }
      return;
    }

    if (id == 'visibilityPublic' || id == 'editingPublic') {
      var visibilityCheckbox = (<HTMLInputElement>document.getElementById('visibilityPublic'));
      var editingCheckbox = (<HTMLInputElement>document.getElementById('editingPublic'));
      if (editingCheckbox.checked) {
        visibilityCheckbox.checked = true;
      }
      return;
    }

    var visibilityCheckbox = (<HTMLInputElement>document.getElementById('visibility' + id));
    var editingCheckbox = (<HTMLInputElement>document.getElementById('editing' + id));
    if (editingCheckbox.checked) {
      visibilityCheckbox.checked = true;
    }

  }

  submitVisibilityChanges() {

    var visibilityCheckbox = (<HTMLInputElement>document.getElementById('visibilityPublic'));
    var editingCheckbox = (<HTMLInputElement>document.getElementById('editingPublic'));
    this.recipe.globalAuthenticationLevel = 0;
    if (editingCheckbox.checked) {
      this.recipe.globalAuthenticationLevel = 2;
    }
    else {
      if (visibilityCheckbox.checked) {
        this.recipe.globalAuthenticationLevel = 1;
      }
    }

    this.recipe.groupsAuthenticationLevel = [];
    for (var i = 0; i < this.userGroups.length; i++) {
      var visibilityCheckbox = (<HTMLInputElement>document.getElementById('visibility' + this.userGroups[i].id));
      var editingCheckbox = (<HTMLInputElement>document.getElementById('editing' + this.userGroups[i].id));
      var groupVisibility = new RecipeVisibility();

      if (editingCheckbox.checked) {
        groupVisibility.groupId = this.userGroups[i].id;
        groupVisibility.authenticationLevel = 2;
        this.recipe.groupsAuthenticationLevel.push(groupVisibility);
      }
      else if (visibilityCheckbox.checked) {
        groupVisibility.groupId = this.userGroups[i].id;
        groupVisibility.authenticationLevel = 1;
        this.recipe.groupsAuthenticationLevel.push(groupVisibility);
      }
    }

  }

  goBack(): void {
    this.location.back();
  }

  toggleEditing(): void {
    console.log(this.recipeService.isUserAllowedToEdit(this.recipe));
    if (this.recipeService.isUserAllowedToEdit(this.recipe)) {
      this.editing = !this.editing;
    }
    else {
      window.alert("Você não possui autorização para realizar modificações nessa receita!");
    }

  }

  toggleFavorite() {
    //this.recipe.isFavourite = !this.recipe.isFavourite;

    if (this.isFavourite) {
      this.recipeService.rmFavourite(this.recipeService.userIdSession, this.recipe.id)
    } else {
      this.recipeService.addFavourite(this.recipeService.userIdSession, this.recipe.id)
    }
    this.updateFavourite();
  }

  getImageSrc(index) {
    return "../../../../backend/uploads/" + this.recipe.photos[index];
  }

  setImageAsFirst(index) {
    var newPhotoList = [];
    newPhotoList.push(this.recipe.photos[index]);
    for (var i = 0; i < this.recipe.photos.length; i++) {
      if (i != index)
        newPhotoList.push(this.recipe.photos[i]);
      //(<HTMLInputElement>document.getElementById('primaryImageRadio'+i)).checked = false;
    }
    this.recipe.photos = newPhotoList;

  }

  addImage(): void {
    if (this.recipeService.isUserAllowedToCreateRecipe()) {
      const files = (<HTMLInputElement>document.getElementById('fileUploader')).files;
      //const url = 'https://receitas.fortesrey.net/backend/upload_file.php'
      const url = "http://localhost:8000/upload_file.php";

      const formData = new FormData()
      for (let i = 0; i < files.length; i++) {
        var file = files[i]
        var fileHashedName = md5((file.lastModified + Date.now() + file.size).toString())
        var filename = fileHashedName + this.recipe.photos.length + '.' + file.name.split('.').pop();
        if (!this.recipe.photos) {
          this.recipe.photos = [];
        }
        this.recipe.photos.push(filename);
        formData.append('files[]', file)
        formData.append('filenames[]', filename)
      }
      fetch(url, {
        method: 'POST',
        body: formData,
        credentials : 'include',
      }).then(response => {
        console.log(response)
      })
    }

  }

  /*
  reuploadImage(imgIndex): void {

  }*/

  deleteImage(imgIndex): void {
    // pode usar recipe.photos[imgIndex] para pegar a URL da imagem e retirar do banco de dados
    this.recipe.photos.splice(imgIndex, 1);
  }

  addIngredient(): void {
    if (this.recipe.ingredients)
      this.recipe.ingredients.push({ id: 10, name: "", amount: null, unit: "" });
    else {
      this.recipe.ingredients = [];
      this.recipe.ingredients.push({ id: 10, name: "", amount: null, unit: "" });
    }
  }

  removeIngredient(index: number): void {
    this.recipe.ingredients.splice(index, 1);
  }

  addDirection(): void {
    if (this.recipe.preparation)
      this.recipe.preparation.push("");
    else {
      this.recipe.preparation = [];
      this.recipe.preparation.push("");
    }

  }

  removeDirection(index: number): void {
    this.recipe.preparation.splice(index, 1);
  }

  removeTag(index: number): void {
    this.recipe.tags.splice(index, 1);
    this.filterAvailableTags();
  }

  addTag(): void {
    if (this.recipe.tags) {
      this.recipe.tags.push(+this.selectedTag);
      this.filterAvailableTags();
    }
    else {
      this.recipe.tags = [];
      this.recipe.tags.push(+this.selectedTag);
      this.filterAvailableTags();
    }
  }

  filterAvailableTags(): void {

    this.availableTags = JSON.parse(JSON.stringify(this.tags));
    for (var i = 0; i < this.tags.length; i++) {
      for (var j = 0; j < this.recipe.tags.length; j++) {
        if (this.tags[i].id.toString() == this.recipe.tags[j].toString()) {
          var index = this.availableTags.map(function (d) { return +d.id }).indexOf(+this.recipe.tags[j]);
          this.availableTags.splice(index, 1);
        }
      }
    }
  }



  save(): void {
    var element = document.getElementById("recipeNameEditor");
    if (!this.recipe.name) {

      element.classList.add("is-invalid");
      window.alert("Por Favor adicione um nome à receita.");
      return;
    }
    else {
      element.classList.remove("is-invalid");
    }
    if (this.recipeService.isUserAllowedToCreateRecipe()) {
      this.toggleEditing();
      this.numberOfDifficultyStars = [];
      for (let i = 0; i < this.recipe.difficulty; i++) {
        this.numberOfDifficultyStars.push(1);
      }
      if (this.newRecipe) {
        this.recipe.userId = this.recipeService.getUserId();
        console.log(this.recipe.userId);
        this.recipeService.saveNewRecipe(this.recipe);
      }
      else
        this.recipeService.editRecipe(this.recipe);
      this.router.navigateByUrl('recipes/all');
    }
    else {
      window.alert("Você não possui autorização para adicionar ou editar receitas!");
    }

  }

  delete(): void {
    if (this.isUserAllowedToEdit()) {
      if (confirm("Você tem certeza que deseja deletar esta receita? Essa ação é irreversível.")) {
        this.recipeService.deleteRecipe(this.recipe);
        this.goBack();
      }
    }
    else {
      window.alert("Você não possui autorização para deletar receitas!");
    }
  }

  isUserAllowedToEdit() {
    console.log("gothere", this.recipeService.isUserAllowedToEdit(this.recipe));
    if (this.recipeService.isUserAllowedToEdit(this.recipe))
      return true;
    else
      return false;
  }

  trackByFn(index: any, item: any) {
    return index;
  }
}
