import { Injectable } from '@angular/core';
import { Recipe } from './recipe';
import { RECIPES } from './mock-recipes';
import { Observable, of } from 'rxjs';
import { MessageService } from './message.service';

@Injectable({
  providedIn: 'root'
})

export class RecipeService {

  constructor(private messageService: MessageService) { }

  getRecipes(): Observable<Recipe[]>
  {
    this.messageService.add('RecipeService: fetched recipes');
    return of(RECIPES);
  } 

  getRecipe(id:number): Observable<Recipe>
  {
    this.messageService.add('RecipeService: fetched this specific recipe id=${id}`');
    return of(RECIPES.find(recipe => recipe.id === id));
  }
}
