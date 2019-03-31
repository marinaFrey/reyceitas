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
  newRecipe: boolean;
  tags: Tag[];
  availableTags: Tag[];
  selectedTag: string;
  userGroups: Group[];

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

    this.recipeService.getTags()
      .subscribe(tags => {
        this.tags = tags;
        this.filterAvailableTags();
      });

    this.recipe =
      {
        id: null,
        name: '',
        photos: [],
        duration: "",
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
          this.numberOfDifficultyStars = Array(+this.recipe.difficulty).fill(1);
          if (!this.recipe.tags)
            this.recipe.tags = [];
          if (!this.recipe.ingredients)
            this.recipe.ingredients = [];
          if (!this.recipe.preparation)
            this.recipe.preparation = [];
          if (!this.recipe.photos)
            this.recipe.photos = [];

          console.log(this.recipe.groupsAuthenticationLevel);
          this.recipeService.getGroupsByRecipe(this.recipe.name).subscribe(authGroups => {
            this.recipe.groupsAuthenticationLevel = authGroups;
          });

        });

      this.newRecipe = false;
      this.editing = false;
    }

  }

  getTagFromId(tagId): string {
    return this.recipeService.searchTagById(tagId, this.tags);
  }

  getGroupFromId(groupVisibility) {
    return this.recipeService.searchGroupById(groupVisibility.groupId, this.userGroups);
  }

  getUserNameFromId(id) {

  }

  initializeVisibilityModal() {
    if (this.recipe.globalAuthenticationLevel) {
      var visibilityCheckbox = (<HTMLInputElement>document.getElementById('visibilityPublic'));
      var editingCheckbox = (<HTMLInputElement>document.getElementById('editingPublic'));
      if (this.recipe.globalAuthenticationLevel == 2) {
        visibilityCheckbox.checked = true;
        editingCheckbox.checked = true;
      }
      else if (this.recipe.globalAuthenticationLevel == 1) {
        visibilityCheckbox.checked = true;
        editingCheckbox.checked = false;
      }
      else {
        visibilityCheckbox.checked = false;
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
    } else if (visibilityCheckbox.checked) {
      this.recipe.globalAuthenticationLevel = 1;
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
    if (this.recipeService.getUserLevel() > 1) {
      this.editing = !this.editing;
    }

    else {
      window.alert("Você não possui autorização para realizar modificações no banco de dados!");
    }

  }

  getImageSrc(index) {
    return "../../../../backend/uploads/" + this.recipe.photos[index];
  }

  addImage(): void {
    const files = (<HTMLInputElement>document.getElementById('fileUploader')).files;
    const url = 'http://localhost:8000/upload_file.php'

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
    }).then(response => {
      console.log(response)
    })
  }

  reuploadImage(imgIndex): void {

  }

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
        if (this.tags[i].id == this.recipe.tags[j]) {
          var index = this.availableTags.map(function (d) { return d.id }).indexOf(this.recipe.tags[j]);
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
    if (this.recipeService.getUserLevel() > 0) {
      this.toggleEditing();
      this.numberOfDifficultyStars = [];
      for (let i = 0; i < this.recipe.difficulty; i++) {
        this.numberOfDifficultyStars.push(1);
      }
      if (this.newRecipe)
      {
        this.recipe.userId = this.recipeService.getUserId();
        this.recipeService.saveNewRecipe(this.recipe);
      }
      else
        this.recipeService.editRecipe(this.recipe);
      this.router.navigateByUrl('recipes/all');
    }
    else {
      window.alert("Você não possui autorização para realizar modificações no banco de dados!");
    }

  }

  delete(): void {
    if (this.recipeService.getUserLevel() > 1) {
      if (confirm("Você tem certeza que deseja deletar esta receita? Essa ação é irreversível.")) {
        this.recipeService.deleteRecipe(this.recipe);
        this.goBack();
      }
    }
    else {
      window.alert("Você não possui autorização para realizar modificações no banco de dados!");
    }
  }

  trackByFn(index: any, item: any) {
    return index;
  }
}
