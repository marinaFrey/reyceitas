import { Component, OnInit, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Location } from '@angular/common';

import { RecipeService }  from '../../recipe.service';
import { Recipe } from '../../recipe';

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

  constructor(private route: ActivatedRoute,
              private recipeService: RecipeService,
              private location: Location) 
  {   }

  ngOnInit() 
  {
    this.getRecipe();
    this.numberOfDifficultyStars = Array(this.recipe.difficulty).fill(1);
    this.editing = false;
  }

  getRecipe(): void
  {
    const id = +this.route.snapshot.paramMap.get('id');
    this.recipeService.getRecipe(id)
      .subscribe(recipe => this.recipe = recipe);
  }

  goBack(): void 
  {
    this.location.back();
  }

  toggleEditing(): void 
  {
    this.editing = !this.editing;
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

  save(): void 
  {
    this.toggleEditing();
    this.numberOfDifficultyStars = [];
    for(let i = 0; i < this.recipe.difficulty; i++)
    {
      this.numberOfDifficultyStars.push(1);
    }
    console.log(this.numberOfDifficultyStars,this.recipe.difficulty);
  }

  trackByFn(index: any, item: any) 
  {
    return index;
  }
}
