import { Injectable } from '@angular/core';
import { Recipe } from './recipe';
import { Tag } from './recipe';
import { ChartFormat } from './recipe';
import { RECIPES } from './mock-recipes';
import { TAGS } from './mock-recipes';
import { Observable, of } from 'rxjs';
import { MessageService } from './message.service';

@Injectable({
  providedIn: 'root'
})

export class RecipeService 
{

  constructor(private messageService: MessageService) { }

  getRecipes(): Observable<Recipe[]> 
  {
    this.messageService.add('RecipeService: fetched recipes');
    return of(RECIPES);
  }

  getTags(): Observable<Tag[]> 
  {
    this.messageService.add('RecipeService: fetched tags');
    return of(TAGS);
  }

  searchTag(this:number, value: Recipe, index: number, obj: Recipe[]) : Recipe
  {
    for(var i=0; i < value.tags.length; i++)
    {
      if(value.tags[i] == this)
        return value; 
    }
  }

  organizeChartData():  Observable<ChartFormat>
  {
    var chartData = 
    {
      labels: [],
      data: [],
      colors: []
    }

    for(var i = 0; i < TAGS.length; i++)
    {
      chartData.labels.push(TAGS[i].name);
      chartData.colors.push(TAGS[i].color);
    }

    for(var j = 0; j < RECIPES.length; j++)
    {
      for(var k = 0; k < RECIPES[j].tags.length; k++)
      {
        if(chartData.data[RECIPES[j].tags[k]])
        {
          chartData.data[RECIPES[j].tags[k]]++;
        }
        else
        {
          chartData.data[RECIPES[j].tags[k]] = 1;
        }
      }
    }
    
    return of(chartData);
  }

  searchRecipesByTag(term: number): Observable<Recipe[]> 
  {
    return of(RECIPES.filter(this.searchTag, term));
  }

  searchTerm(this:string, value: Recipe, index: number, obj: Recipe[]) : Recipe
  {
    if( value.name.toUpperCase().indexOf(this.toUpperCase()) >= 0 ){
      return value;
    }
    for(var i=0; i < value.ingredients.length; i++)
    {
      if( value.ingredients[i].name.indexOf(this) >= 0 ){
        return value;
      }
    }
    for(var i=0; i < value.preparation.length; i++)
    {
      if( value.preparation[i].indexOf(this) >= 0 ){
        return value;
      }
    }
  }
  searchRecipesByTerm(term: string): Observable<Recipe[]>
  {
    if (!term.trim()) 
    {
      return of([]);
    }
    return of(RECIPES.filter(this.searchTerm, term));
  }

  getRecipe(id: number): Observable<Recipe> 
  {
    this.messageService.add('RecipeService: fetched this specific recipe id=${id}`');
    return of(RECIPES.find(recipe => recipe.id === id));
  }

  getNumberOfRecipes(): Observable<number>
  {
    return of(RECIPES.length);
  }
}
