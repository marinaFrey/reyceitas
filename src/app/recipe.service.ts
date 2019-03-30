import { Injectable } from '@angular/core';
import { Recipe } from './recipe';
import { Tag } from './recipe';
import { User } from './recipe';
import { ChartFormat } from './recipe';
// import { RECIPES } from './mock-recipes';
//import { TAGS } from './mock-recipes';
import { Observable, of } from 'rxjs';
import { MessageService } from './message.service';
import { HttpClient, HttpParams } from '@angular/common/http';
import { map } from 'rxjs/operators';


@Injectable({
  providedIn: 'root'
})

export class RecipeService {

  userLevel = 2;
    //Session variables
    isLoggedIn: boolean;
    userIdSession: number;
    usernameSession: string;
    fullnameSession: string;
    emailSession: string;

  constructor(private messageService: MessageService,
    private httpCli: HttpClient) { }

    login(user: User): void
    {
        this.userIdSession = user.id;
        this.usernameSession = user.username;
        this.fullnameSession = user.fullname;
        this.emailSession = user.email;
        this.isLoggedIn = true;
        console.log("logged in")
    }
    logout(): void
    {
        this.isLoggedIn = false;
        console.log("logged out")
    }
  getRecipes(): Observable<Recipe[]> {
    this.messageService.add('RecipeService: fetched recipes');

    var recps = this.httpCli.get<Recipe[]>(
      `http://localhost:8000/get_recipe_permissions.php`);
    return recps;
    // return of(RECIPES);
  }
  getRecipesPerUser(username: string): Observable<Recipe[]> {
    this.messageService.add('RecipeService: fetched recipes');

    var recps = this.httpCli.get<Recipe[]>(
      `http://localhost:8000/get_recipe_permissions.php?username=${username}`);
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
      console.log("saved recipe", data);
      return data;
    }, (error) => {
      console.log("error!", error);
    });

  }

  editRecipe(recipe) {
    console.log(recipe);
    this.messageService.add('RecipeService: edited recipe');
    let param: any = { 'recipe': JSON.stringify(recipe) };
    let params = new HttpParams();
    var recps = this.httpCli.get<Recipe[]>(
      "http://localhost:8000/edit_recipe.php", { params: param });
    var t = recps.subscribe((data) => {
      console.log("edited recipe", data);
    }, (error) => {
      console.log("error!", error);
    });

  }

  deleteRecipe(recipe) {
    console.log(recipe);
    this.messageService.add('RecipeService: deleted recipe');
    let param: any = { 'id': recipe.id.toString() };
    let params = new HttpParams();
    var recps = this.httpCli.get<Recipe[]>(
      "http://localhost:8000/delete_recipe.php", { params: param });
    var t = recps.subscribe((data) => {
      console.log("deleted recipe");

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
  getUsers(): Observable<User[]> {
    this.messageService.add('RecipeService: fetched users');

    var users = this.httpCli.get<User[]>(
      "http://localhost:8000/get_users.php");
    return users;
  }
  searchUsers(username: string): Observable<User[]> {
    this.messageService.add('RecipeService: fetched users');

    var users = this.httpCli.get<User[]>(
      `http://localhost:8000/get_users.php?username=${username}`);
    return users;
  }

  searchTag(this: number, value: Recipe, index: number, obj: Recipe[]): Recipe {
    if (value.tags) {
      for (var i = 0; i < value.tags.length; i++) {
        if (value.tags[i] == this)
          return value;
      }
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
  editUser(user: User): Observable<number> {
    this.messageService.add('RecipeService: edit user');
    let param: any = { 'user_edit': JSON.stringify(user) };
    let params = new HttpParams();
    var user_id;
    var result = this.httpCli.get<number>("http://localhost:8000/save_user.php", { params: param });
    return result;
  }
  newUser(user: User): Observable<number> {
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

    this.getRecipes().subscribe(num => {
      var recipes = num
      this.getTags().subscribe(t => {
        var tags = t;
        for (var i = 0; i < tags.length; i++) {
          chartData.labels.push(tags[i].name);
          chartData.colors.push(tags[i].color);
        }
        var dummy = [];
        for (var j = 0; j < recipes.length; j++) {
          for (var k = 0; k < recipes[j].tags.length; k++) {

            if (dummy[recipes[j].tags[k]]) {
              dummy[recipes[j].tags[k]]++;
            }
            else {
              dummy[recipes[j].tags[k]] = 1;
            }
          }
        }

        for (var l = 0; l < tags.length; l++) {
          if (dummy[tags[l].id])
            chartData.data.push(dummy[tags[l].id]);
          else
            chartData.data.push(0);
        }

        return of(chartData);
      });
    }
    );
    return of(chartData);
  }

  searchRecipesByTag(term: number): Observable<Recipe[]> {

    return this.getRecipes().pipe(
      map((recs: Recipe[]) => {
        return recs.filter(this.searchTag, term)
      }));

    /*
    return this.getRecipes().pipe(
      map((recs: Recipe[]) => {
        return recs.filter(this.searchTerm, term)
      }));*/

  }

  searchTagById(tagId, tagList): string {
    if (tagList) {
      for (var i = 0; i < tagList.length; i++) {
        if (tagList[i].id == tagId) {
          return tagList[i].name;
        }
      }
    }
  }

  searchGroupById(groupId, groupList): string {
    if (groupList) {
      for (var i = 0; i < groupList.length; i++) {
        if (groupList[i].id == groupId) {
          return groupList[i].name;
        }
      }
    }
  }

  searchTerm(this: string, value: Recipe, index: number, obj: Recipe[]): Recipe {

    if (value.name && (value.name.toUpperCase().indexOf(this.toUpperCase()) >= 0)) {
      return value;
    }
    if (value.ingredients) {
      for (var i = 0; i < value.ingredients.length; i++) {
        if (value.ingredients[i].name.indexOf(this) >= 0) {
          return value;
        }
      }
    }
    if (value.preparation) {
      for (var i = 0; i < value.preparation.length; i++) {
        if (value.preparation[i].indexOf(this) >= 0) {
          return value;
        }
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

  getNumberOfUsers(): Observable<number> {
    // return of(RECIPES.length);

    return this.getUsers().pipe(
      map((recs: User[]) => {

        return recs.length
      }));
  }

  getUserLevel() {
    return this.userLevel;
  }
}
