import { Component, OnInit } from '@angular/core';
import { Recipe } from '../recipe';
import { Tag } from '../recipe';
import { ActivatedRoute } from '@angular/router';
import { RecipeService } from '../recipe.service';
import { Location } from '@angular/common';

@Component({
  selector: 'app-recipes',
  templateUrl: './recipes.component.html',
  styleUrls: ['./recipes.component.css']
})
export class RecipesComponent implements OnInit 
{

  recipes: Recipe[];
  searchTerm: string;

  constructor(private route: ActivatedRoute,
    private recipeService: RecipeService,
    private location: Location) 
  {
    route.params.subscribe(val => 
    {
      this.getRecipes();
    });
  }

  ngOnInit() 
  {
    this.getRecipes(); 
  }
  
  getRecipes(): void
  {
    var term = this.route.snapshot.paramMap.get('term');
    var termNumber = +term;
    

    if(isNaN(termNumber))
    {
      if(term == "all")
      {
        this.recipeService.getRecipes().subscribe(recipes => this.recipes = recipes);
      }
      else
      {
        this.recipeService.searchRecipesByTerm(term).subscribe(recipes => this.recipes = recipes);
        this.searchTerm = term;
      }
    }
    else
    {
      this.recipeService.searchRecipesByTag(termNumber).subscribe(recipes => this.recipes = recipes);
      var tagList;
      this.recipeService.getTags().subscribe(tags => {
        tagList = tags;
        this.searchTerm = this.recipeService.searchTagById(termNumber,tagList);
      });
      
    }
    
  }

}
