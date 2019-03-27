import { Component, OnInit, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Location } from '@angular/common';

import { RecipeService }  from '../../recipe.service';
import { Recipe } from '../../recipe';
import { Tag } from "../../recipe";

@Component({
  selector: 'app-recipe-details',
  templateUrl: './recipe-details.component.html',
  styleUrls: ['./recipe-details.component.css']
})
export class RecipeDetailsComponent implements OnInit 
{

  @Input() recipe: Recipe;
  numberOfDifficultyStars: number[];
  editing: boolean;
  newRecipe: boolean;
  tags: Tag[];
  availableTags : Tag[];
  selectedTag: string;

  constructor(private route: ActivatedRoute,
              private recipeService: RecipeService,
              private location: Location) 
  {   }

  ngOnInit() 
  {
    this.getRecipe();
  }

  getRecipe(): void
  {
    this.recipeService.getTags()
        .subscribe(tags => {
          this.tags = tags;
          this.filterAvailableTags();
        });

    const id = +this.route.snapshot.paramMap.get('id');
    if(isNaN(id))
    {
      this.recipe = 
      { 
        id: null, 
        name: '', 
        photos:[], 
        duration: "", 
        difficulty: null, 
        servings: null, 
        description: '', 
        ingredients: [{id:null,name:"",amount:null,unit:""}], 
        preparation: [""],
        tags:[]
      };

        this.editing = true;
        this.newRecipe = true;
    }
    else
    {
      this.recipeService.getRecipe(id)
        .subscribe(recipe => {
          this.recipe = recipe;
          this.numberOfDifficultyStars = Array(this.recipe.difficulty).fill(1);
        });
      


      this.newRecipe = false;
      this.editing = false;
    }
    
  }

  goBack(): void 
  {
    this.location.back();
  }

  toggleEditing(): void 
  {
    this.editing = !this.editing;
  }

  addImage(): void
  {

  }

  reuploadImage(): void
  {

  }

  deleteImage(imgIndex): void
  {
    // pode usar recipe.photos[imgIndex] para pegar a URL da imagem e retirar do banco de dados
    this.recipe.photos.splice(imgIndex,1);
  } 

  addIngredient(): void 
  {
    this.recipe.ingredients.push({id:10,name:"",amount:null,unit:""});
  }

  removeIngredient(index: number): void
  {
    this.recipe.ingredients.splice(index,1);
  }

  addDirection(): void 
  {
    this.recipe.preparation.push("");
  }

  removeDirection(index: number): void
  {
    this.recipe.preparation.splice(index,1);
  }

  removeTag(index: number): void
  {
    this.recipe.tags.splice(index,1);
  }

  addTag(): void
  {
    this.recipe.tags.push(+this.selectedTag);
    this.filterAvailableTags();
  }

  filterAvailableTags(): void
  {
    this.availableTags = JSON.parse(JSON.stringify(this.tags));
    for(var i=0; i< this.tags.length; i++)
    {
      for(var j=0; j < this.recipe.tags.length; j++)
      {
        if(this.tags[i].id == this.recipe.tags[j])
        {
          var index = this.availableTags.map(function(d){return d.id}).indexOf(this.recipe.tags[j]);
          this.availableTags.splice(index,1);
        }
      }
    }
  }

  save(): void 
  {
    this.toggleEditing();
    this.numberOfDifficultyStars = [];
    for(let i = 0; i < this.recipe.difficulty; i++)
    {
      this.numberOfDifficultyStars.push(1);
    }
  }

  trackByFn(index: any, item: any) 
  {
    return index;
  }
}
