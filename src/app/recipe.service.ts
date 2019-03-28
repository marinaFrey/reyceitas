import { Injectable } from '@angular/core';
import { Recipe } from './recipe';
import { Tag } from './recipe';
import { User } from './recipe';
import { ChartFormat } from './recipe';
// import { RECIPES } from './mock-recipes';
// import { TAGS } from './mock-recipes';
import { Observable, of } from 'rxjs';
import { MessageService } from './message.service';
import { HttpClient, HttpParams } from '@angular/common/http';
import { map } from 'rxjs/operators';


@Injectable({
  providedIn: 'root'
})

export class RecipeService {

  userLevel = 2;

  constructor(private messageService: MessageService,
    private httpCli: HttpClient) { }

  getRecipes(): Observable<Recipe[]> {
    this.messageService.add('RecipeService: fetched recipes');

    var recps = this.httpCli.get<Recipe[]>(
      "http://localhost:8000/get_recipes.php");


    return recps;
    // return of(RECIPES);
  }

  saveNewRecipe(recipe) {
    console.log(recipe);
    this.messageService.add('RecipeService: saved new recipe');
    let param: any = { 'recipe': JSON.stringify(recipe) };
    let params = new HttpParams();
    var recps = this.httpCli.get<Recipe[]>(
      "http://localhost:8000/save_recipe.php", { params: param });
    var t = recps.subscribe((data) => {
      console.log("got some data from backend", data);

    }, (error) => {
      console.log("error!", error);
    });

  }

  deleteRecipe(recipe) {
    console.log(recipe);
    this.messageService.add('RecipeService: saved new recipe');
    let param: any = { 'id': recipe.id.toString() };
    let params = new HttpParams();
    var recps = this.httpCli.get<Recipe[]>(
      "http://localhost:8000/delete_recipe.php", { params: param });
    var t = recps.subscribe((data) => {
      console.log("got some data from backend", data);

    }, (error) => {
      console.log("error!", error);
    });

  }

  getTags(): Observable<Tag[]> {
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

  searchTag(this: number, value: Recipe, index: number, obj: Recipe[]): Recipe {
    for (var i = 0; i < value.tags.length; i++) {
      if (value.tags[i] == this)
        return value;
    }
  }
  /*
  newUser(user: User): number 
  {
    this.messageService.add('RecipeService: new user');

    var str_json = (JSON.stringify(user).replace(/ /g,'\ '))
    console.log(str_json);
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("POST", "http://localhost:8000/save_user.php", false);
    xmlHttp.setRequestHeader("Content-type", "application/json");
    xmlHttp.send(str_json);
    console.log(xmlHttp.response);

    return 0;
    }*/
    newUser(user: User): Observable<number> 
    {
        this.messageService.add('RecipeService: new user');
        let param: any = { 'user': JSON.stringify(user) };
        let params = new HttpParams();
        var user_id;
        var result = this.httpCli.get<number>("http://localhost:8000/save_user.php", { params: param });
        return result;
    }

  organizeChartData(): Observable<ChartFormat> {
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

  searchRecipesByTag(term: number): Observable<Recipe[]> {
    // return of(RECIPES.filter(this.searchTag, term));

    return this.getRecipes().pipe(
      map((recs: Recipe[]) => {
        return recs.filter(this.searchTerm, term)
      }));


    // var recips = this.getRecipes();
    // return of(RECIPES.find(recipe => recipe.id === id));

    // return recips;


  }

  searchTerm(this: string, value: Recipe, index: number, obj: Recipe[]): Recipe {
    if (value.name.toUpperCase().indexOf(this.toUpperCase()) >= 0) {
      return value;
    }
    for (var i = 0; i < value.ingredients.length; i++) {
      if (value.ingredients[i].name.indexOf(this) >= 0) {
        return value;
      }
    }
    for (var i = 0; i < value.preparation.length; i++) {
      if (value.preparation[i].indexOf(this) >= 0) {
        return value;
      }
    }
  }
  searchRecipesByTerm(term: string): Observable<Recipe[]> {
    if (!term.trim()) {
      return of([]);
    }

    // return of(RECIPES.filter(this.searchTerm, term));
    return this.getRecipes().pipe(
      map((recs: Recipe[]) => {
        return recs.filter(this.searchTerm, term)
      }));


  }

  getRecipe(id: number): Observable<Recipe> {
    this.messageService.add('RecipeService: fetched this specific recipe id=${id}`');
    
    return this.getRecipes().pipe(
      map((recs: Recipe[]) => {
        return recs.find(recipe => recipe.id === id)
      }));

  }

  getNumberOfRecipes(): Observable<number> {
    // return of(RECIPES.length);

    return this.getRecipes().pipe(
      map((recs: Recipe[]) => {

        return recs.length
      }));
  }

  getUserLevel()
  {
    return this.userLevel;
  }
}
