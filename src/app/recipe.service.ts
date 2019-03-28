import { Injectable } from '@angular/core';
import { Recipe } from './recipe';
import { Tag } from './recipe';
import { User } from './recipe';
import { ChartFormat } from './recipe';
// import { RECIPES } from './mock-recipes';
// import { TAGS } from './mock-recipes';
import { Observable, of } from 'rxjs';
import { MessageService } from './message.service';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import { map } from 'rxjs/operators';


@Injectable({
  providedIn: 'root'
})

export class RecipeService 
{

  constructor(private messageService: MessageService, 
    private httpCli: HttpClient) {}

  // constructor(private messageService: MessageService) {}
  
  getRecipes(): Observable<Recipe[]> 
  {
    this.messageService.add('RecipeService: fetched recipes');


    var recps = this.httpCli.get<Recipe[]>(
      "http://localhost:8000/get_recipes.php");
    
    // var recps = this.httpCli.get(
        // "http://localhost:8000/get_recipes.php");

    
    // window.alert( JSON.stringify(recps) );

    return recps;
    // return of(RECIPES);
  }

  getTags(): Observable<Tag[]> 
  {
    this.messageService.add('RecipeService: fetched tags');

    var tags = this.httpCli.get<Tag[]>(
      "http://localhost:8000/list_tags.php");
    
    

    // this.httpCli.get(
        // "http://localhost:8000/list_tags.php").subscribe((res)=>{
          // window.alert("aa");
          // window.alert(JSON.stringify(res));
      // });
    // window.alert("bb");
    
    // window.alert( JSON.stringify(tags) );

    return tags;
    // return aaa;
    // this.messageService.add("");
    // return of(a);
    // return of(TAGS);


  }
  getUsers(): Observable<User[]> 
  {
    this.messageService.add('RecipeService: fetched users');

    var users = this.httpCli.get<User[]>(
      "http://localhost:8000/get_users.php");
    return users;
  }
  searchUsers(username: string): Observable<User[]> 
  {
    this.messageService.add('RecipeService: fetched users');

    var users = this.httpCli.get<User[]>(
        `http://localhost:8000/get_users.php?username=${username}`);
    return users;
  }

  searchTag(this:number, value: Recipe, index: number, obj: Recipe[]) : Recipe
  {
    for(var i=0; i < value.tags.length; i++)
    {
      if(value.tags[i] == this)
        return value; 
    }
  }
  newUser(user: User): number 
  {
    this.messageService.add('RecipeService: new user');

    var str_json = (JSON.stringify(user))
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("POST", "http://localhost:8000/save_user.php", false);
    xmlHttp.setRequestHeader("Content-type", "application/json");
    xmlHttp.send(str_json);
    console.log(xmlHttp.response);

    return 0;
  }

  organizeChartData():  Observable<ChartFormat>
  {
    var chartData = 
    {
      labels: [],
      data: [],
      colors: []
    }

    // for(var i = 0; i < TAGS.length; i++)
    // {
    //   chartData.labels.push(TAGS[i].name);
    //   chartData.colors.push(TAGS[i].color);
    // }

    // for(var j = 0; j < RECIPES.length; j++)
    // {
    //   for(var k = 0; k < RECIPES[j].tags.length; k++)
    //   {
    //     if(chartData.data[RECIPES[j].tags[k]])
    //     {
    //       chartData.data[RECIPES[j].tags[k]]++;
    //     }
    //     else
    //     {
    //       chartData.data[RECIPES[j].tags[k]] = 1;
    //     }
    //   }
    // }
    
    return of(chartData);
  }

  searchRecipesByTag(term: number): Observable<Recipe[]> 
  {
    // return of(RECIPES.filter(this.searchTag, term));

    return this.getRecipes().pipe(
      map( (recs  : Recipe[])  => {
        return recs.filter(this.searchTerm, term)
      } ));


    // var recips = this.getRecipes();
    // return of(RECIPES.find(recipe => recipe.id === id));
    
    // return recips;


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

    // return of(RECIPES.filter(this.searchTerm, term));
    return this.getRecipes().pipe(
      map( (recs  : Recipe[])  => {
        return recs.filter(this.searchTerm, term)
      } ));


  }

  getRecipe(id: number): Observable<Recipe> 
  {
    this.messageService.add('RecipeService: fetched this specific recipe id=${id}`');
    // var recips = this.getRecipes();
    // return of(RECIPES.find(recipe => recipe.id === id));
    
    
    // var oneRec = recips.subscribe( (recps : Recipe[]) => {
        // console.log("F");
        // return of(recps.find(recipe => recipe.id === id));
    // } );
    
    return this.getRecipes().pipe(
        map( (recs  : Recipe[])  => {
          return recs.find(recipe => recipe.id === id)
        } ));
    
  }

  getNumberOfRecipes(): Observable<number>
  {
    // return of(RECIPES.length);

    return this.getRecipes().pipe(
      map( (recs  : Recipe[])  => {
        
        // console.log(recs)

        return recs.length
      } ));


    // var recips = this.getRecipes();
    // var lenRec;
    // recips.subscribe( (recps : Recipe[]) => {
    //   lenRec = of(recps.length);
    // } );
    
    // return lenRec;



    // var recipes = this.httpCli.get<Recipe[]>(
      // "http://localhost:8000/get_recipes.php");
    
    // recipes.

    // return recipes;
  }
}
