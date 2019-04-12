import { Component, OnInit } from '@angular/core';
import { Recipe, RecipeView } from '../recipe';
import { Tag } from '../recipe';
import { ActivatedRoute } from '@angular/router';
import { RecipeService } from '../recipe.service';
import { Location } from '@angular/common';

@Component({
  selector: 'app-recipes',
  templateUrl: './recipes.component.html',
  styleUrls: ['./recipes.component.css']
})

export class RecipesComponent implements OnInit {
  isReady: number;
  recipes: Recipe[];
  recipeViews: RecipeView[];
  recipeFavourite: boolean[];
  recipeOwned: boolean[];
  searchTerm: string;
  term: string;

  constructor(private route: ActivatedRoute,
    private recipeService: RecipeService,
    private location: Location) {
    route.params.subscribe(val => {
      this.getRecipes();
    });
  }

  ngOnInit() {
    this.getRecipes();
    this.recipeFavourite = [];
    this.recipeOwned = [];
    this.recipes = [];
  }

  getImageSrc(index, recipe) {
    return "../../../../backend/uploads/" + recipe.photos[index];
  }

  updateRecipeViews(recipes) {
    this.recipeViews = [];
    for (var i = 0; i < recipes.length; i++) {
      this.recipeViews[i] = new RecipeView();
      this.recipeViews[i].recipe = recipes[i];
    }
  }

  updateFavouriteRecipes(favs: String[]) {
    for (var i = 0; i < this.recipeViews.length; i++) {
      //this.recipeFavourite[i] = ((favs != null) && favs.includes(this.recipes[i].id.toString()))
      this.recipeViews[i].isFavourite = ((favs != null) && favs.includes(this.recipeViews[i].recipe.id.toString()));
    }
  }
  updateOwnedRecipes(own_recipes: String[]) {
    for (var i = 0; i < this.recipeViews.length; i++) {
      //this.recipeOwned[i] = ((own_recipes != null) && own_recipes.includes(this.recipes[i].id.toString()))
      this.recipeViews[i].isOwned = ((own_recipes != null) && own_recipes.includes(this.recipeViews[i].recipe.id.toString()));
    }
    console.log(this.recipeViews);
  }

  filterFavouriteRecipes() {
    var selectedRecipes = [];
    for (var i = 0; i < this.recipeViews.length; i++) {
      if (this.recipeViews[i].isFavourite)
        selectedRecipes.push(this.recipeViews[i]);
    }
    this.recipeViews = selectedRecipes;
  }

  filterOwnedRecipes() {
    var selectedRecipes = [];
    for (var i = 0; i < this.recipeViews.length; i++) {
      if (this.recipeViews[i].isOwned)
        selectedRecipes.push(this.recipeViews[i]);
    }
    this.recipeViews = selectedRecipes;
  }

  getRecipes(): void {
    var term = this.route.snapshot.paramMap.get('term');
    this.term = term;
    var termNumber = +term;
    this.isReady = 0;

    if (isNaN(termNumber)) {
      if (term == "all" || term == "favs" || term == "owned") {
        // getting recipes
        this.recipeService.getRecipes().subscribe(recipes => {
          this.updateRecipeViews(recipes);
          this.isReady++;

          // getting which recipes are owned by logged user
          this.recipeService.getOwnedRecipes()
            .subscribe(owned => {
              this.updateOwnedRecipes(owned);
              if (term == "owned") {
                this.filterOwnedRecipes();
              }
              this.isReady++;
            });
          // getting which recipes are favourite by logged user
          this.recipeService.getFavourites()
            .subscribe(favs => {
              this.updateFavouriteRecipes(favs);
              if (term == "favs") {
                this.filterFavouriteRecipes();
              }
              this.isReady++;
            });
        });
      }
      else {
        // searching by text
        this.recipeService.searchRecipesByTerm(term).subscribe(recipes => {
          this.updateRecipeViews(recipes);
          this.isReady++;

          // getting which recipes are owned by logged user
          this.recipeService.getOwnedRecipes()
            .subscribe(owned => {
              this.updateOwnedRecipes(owned);
              this.isReady++;
            });
          // getting which recipes are favourite by logged user
          this.recipeService.getFavourites()
            .subscribe(favs => {
              this.updateFavouriteRecipes(favs);
              this.isReady++;
            });
        });
        this.searchTerm = term;
      }
    }
    else {
      // searching by tag
      this.recipeService.searchRecipesByTag(termNumber).subscribe(recipes => {
        this.updateRecipeViews(recipes);
        // getting which recipes are owned by logged user
        this.recipeService.getOwnedRecipes()
          .subscribe(owned => {
            this.updateOwnedRecipes(owned);
            this.isReady++;
          });
        // getting which recipes are favourite by logged user
        this.recipeService.getFavourites()
          .subscribe(favs => {
            this.updateFavouriteRecipes(favs);
            this.isReady++;
          });
      });
      var tagList;
      this.recipeService.getTags().subscribe(tags => {
        tagList = tags;
        this.searchTerm = this.recipeService.searchTagById(termNumber, tagList);
        this.isReady = 3;
      });

    }

  }

}
