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

  constructor(private route: ActivatedRoute,
              private recipeService: RecipeService,
              private location: Location) 
  {   }

  ngOnInit() 
  {
    this.getRecipe();
    this.numberOfDifficultyStars = Array(this.recipe.difficulty).fill(1);
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

}
