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
  recipeFavourite: boolean[];
  recipeOwned: boolean[];
  searchTerm: string;
  term: string;

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
    this.recipeFavourite = [];
    this.recipeOwned= [];
  }

  getImageSrc(index, recipe)
  {
    return "../../../../backend/uploads/" + recipe.photos[index];
  }

  updateFavouriteRecipes(favs:String[])
  {
    for (var i = 0; i < this.recipes.length; i++)
    {
        this.recipeFavourite[i] = ((favs!=null)&&favs.includes(this.recipes[i].id.toString()))
    }
  }
  updateOwnedRecipes(own_recipes:String[])
  {
    for (var i = 0; i < this.recipes.length; i++)
    {
        this.recipeOwned[i] = ((own_recipes!=null)&&own_recipes.includes(this.recipes[i].id.toString()))
    }
  }
  
  getRecipes(): void
  {
    var term = this.route.snapshot.paramMap.get('term');
    this.term = term;
    var termNumber = +term;
    

    if(isNaN(termNumber))
    {
      if(term == "all")
      {
        this.recipeService.getRecipes().subscribe(recipes => this.recipes = recipes);
        this.recipeService.getOwnedRecipes(this.recipeService.usernameSession)
            .subscribe(owned=> this.updateFavouriteRecipes(owned));
        this.recipeService.getFavourites(this.recipeService.usernameSession)
            .subscribe(favs => this.updateFavouriteRecipes(favs));
      }
      else if(term == "favs")
      {
        this.recipeService.getRecipes().subscribe(recipes => this.recipes = recipes);
        this.recipeService.getOwnedRecipes(this.recipeService.usernameSession)
            .subscribe(owned=> this.updateFavouriteRecipes(owned));
        this.recipeService.getFavourites(this.recipeService.usernameSession)
            .subscribe(favs => this.updateFavouriteRecipes(favs));
      }
      else if(term == "owned")
      {
        this.recipeService.getRecipes().subscribe(recipes => this.recipes = recipes);
        this.recipeService.getOwnedRecipes(this.recipeService.usernameSession)
            .subscribe(owned=> this.updateFavouriteRecipes(owned));
        this.recipeService.getFavourites(this.recipeService.usernameSession)
            .subscribe(favs => this.updateFavouriteRecipes(favs));
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
