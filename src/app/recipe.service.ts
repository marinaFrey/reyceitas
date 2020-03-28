import { Injectable } from '@angular/core';
import { Recipe } from './recipe';
import { Tag } from './recipe';
import { User } from './recipe';
import { ChartFormat, RecipeVisibility } from './recipe';
// import { RECIPES } from './mock-recipes';
//import { TAGS } from './mock-recipes';
import { Observable, of } from 'rxjs';
import {Subject} from 'rxjs/Subject';
import { MessageService } from './message.service';
import { HttpClient, HttpParams, HttpHeaders } from '@angular/common/http';
import { map } from 'rxjs/operators';
import { UserService } from './user.service';


@Injectable({
  providedIn: 'root'
})

export class RecipeService {

  //Session variables
  isLoggedIn: boolean;
  userIdSession: number;
  usernameSession: string;
  fullnameSession: string;
  emailSession: string;
  userLevel: number;
  userGroups: Array<number>;
  distURL = "https://receitas.fortesrey.net/backend/";
  testingURL = "http://localhost:8000/";



  constructor(private messageService: MessageService,
    private httpCli: HttpClient, private userService : UserService) {

    }

  login(user: User): void {
    this.userIdSession = user.id;
    this.usernameSession = user.username;
    this.fullnameSession = user.fullname;
    this.emailSession = user.email;
    this.userLevel = user.authenticationLevel;
    this.userGroups = user.groups;
    this.isLoggedIn = true;
    console.log(user.authenticationLevel);
    console.log("logged in");
  }

  logout(): void {
    this.isLoggedIn = false;
    console.log("logged out");
    this.userService.logout();
  }
  getRecipes(): Observable<Recipe[]> {
    this.messageService.add('RecipeService: fetched recipes');
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };

    if(this.isLoggedIn)
    {
        var recps = this.httpCli.get<Recipe[]>(
          this.testingURL + `get_recipe_permissions.php?username=${this.usernameSession}`, httpOptions);
    }else{
        var recps = this.httpCli.get<Recipe[]>(
          this.testingURL + `get_recipe_permissions.php`, httpOptions);
    }
    return recps;
    // return of(RECIPES);
  }

  saveNewRecipe(recipe) {
    console.log(recipe);
    this.messageService.add('RecipeService: saved new recipe');
    let param: any = { 'recipe': JSON.stringify(recipe) };
    let params = new HttpParams();
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      params: param,
      withCredentials : true
    };

    var recps = this.httpCli.get<Recipe[]>(
      this.testingURL + "save_recipe.php", httpOptions);
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
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      params: param,
      withCredentials : true
    };

    var recps = this.httpCli.get<Recipe[]>(
      this.testingURL + "edit_recipe.php", httpOptions);
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
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      params: param,
      withCredentials : true
    };
    var recps = this.httpCli.get<Recipe[]>(
      this.testingURL + "delete_recipe.php", httpOptions);
    var t = recps.subscribe((data) => {
      console.log("deleted recipe");

    }, (error) => {
      console.log("error!", error);
    });

  }

  getGroupsByRecipe(recipeName): Observable<RecipeVisibility[]> {
    this.messageService.add('RecipeService: fetched recipes');
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };

    var recps = this.httpCli.get<RecipeVisibility[]>(
      this.testingURL + `get_recipe_permissions.php?recipe_name=${recipeName}`, httpOptions);
    return recps;
  }

  getGroups() {
    // TODO: ????
    this.messageService.add('RecipeService: fetched groups');

    var tags = this.httpCli.get<Tag[]>(
      this.testingURL + "get_groups.php");

    return tags;
  }

  getTags(): Observable<Tag[]> {
    this.messageService.add('RecipeService: fetched tags');

    var tags = this.httpCli.get<Tag[]>(
      this.testingURL + "list_tags.php");

    return tags;
  }
  rmGroup(groupId)
  {
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };
    this.httpCli.get<string[]>(
      this.testingURL + `groups.php?rm_group_id=${groupId}`, httpOptions).subscribe();
  }
  editGroup(group)
  {
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };
    var groupJson = JSON.stringify(group).replace(/#/g,'%23');
    console.log(groupJson)
    this.httpCli.get<string[]>(
        this.testingURL + `groups.php?edit_group=${groupJson}`, httpOptions).subscribe();
  }
  addGroup(group)
  {
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };
    var groupJson = JSON.stringify(group).replace(/#/g,'%23');
    console.log(groupJson);
    var new_group = this.httpCli.get<number>(
      this.testingURL + `groups.php?add_group=${groupJson}`, httpOptions)
    return new_group;
  }
  rmTag(tagId)
  {
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };
    this.httpCli.get<string[]>(
      this.testingURL + `tags.php?rm_tag_id=${tagId}`, httpOptions).subscribe();
  }
  editTag(tag)
  {
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };
    var tagJson = JSON.stringify(tag).replace(/#/g,'%23');
    console.log(tagJson)
    this.httpCli.get<string[]>(
        this.testingURL + `tags.php?edit_tag=${tagJson}`, httpOptions).subscribe();
  }
  addTag(tag)
  {
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };
    var tagJson = JSON.stringify(tag).replace(/#/g,'%23');
    console.log(tagJson);
    this.httpCli.get<string[]>(
      this.testingURL + `tags.php?add_tag=${tagJson}`, httpOptions).subscribe();
  }

  getOwnedRecipes(): Observable<string[]>
  {
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };
    var favs = this.httpCli.get<string[]>(
      this.testingURL + `get_recipe_permissions.php?owned_recipes_per_user_id=${this.userIdSession}`, httpOptions);
    return favs;
  }
  getFavourites(): Observable<string[]>
  {
    this.messageService.add('RecipeService: fetched favourites');
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };

    var favs = this.httpCli.get<string[]>(
      this.testingURL + `favourites.php?username=${this.usernameSession}`, httpOptions);

    return favs;
  }
  addFavourite(userId,recipeId)
  {
    this.messageService.add('RecipeService: add favourite');
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };

    this.httpCli.get<number>(
        this.testingURL + `favourites.php?user_id=${userId}&add_favourite=${recipeId}`, httpOptions).subscribe();
  }
  rmFavourite(userId,recipeId)
  {
    this.messageService.add('RecipeService: rm favourite');
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };
    this.httpCli.get<string[]>(
        this.testingURL + `favourites.php?user_id=${userId}&rm_favourite=${recipeId}`, httpOptions).subscribe()
  }
  isFavourite(recipeId,username): Observable<boolean>
  {
      var isFav = new Subject<boolean>()
          this.getFavourites()
          .subscribe(favs => { isFav.next((favs!=null) && (favs.includes(recipeId))); })
          return isFav;
  }
  getUserGroups(userId): Observable<string[]>
  {
    this.messageService.add('RecipeService: fetched favourites');
    var ugroups = this.httpCli.get<string[]>(
      this.testingURL + `user_groups.php?get_groups_user=${userId}`);
    return ugroups;
  }
  setUserGroups(userId, userGroups)
  {
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };
    this.httpCli.get(
        this.testingURL + `user_groups.php?set_groups_user=${userId}&groups=${userGroups}`, httpOptions).subscribe();
  }
  


  getUsernameById(id): Observable<string> {
    this.messageService.add('RecipeService: fetched users');
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };
    
    var username = this.httpCli.get<string>(
      this.testingURL + `get_users.php?id=${id}`, httpOptions);
    console.log(username)
    return username;
  }
  getUsers(): Observable<User[]> {
    this.messageService.add('RecipeService: fetched users');
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };

    var users = this.httpCli.get<User[]>(
      this.testingURL + "get_users.php", httpOptions);
    return users;
  }
  searchUsers(username: string): Observable<User[]> {
    this.messageService.add('RecipeService: fetched users');
    const httpOptions =  { 
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      }),
      withCredentials : true
    };

    var users = this.httpCli.get<User[]>(
      this.testingURL + `get_users.php?username=${username}`, httpOptions);
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
    xmlHttp.open("POST", this.testingURL+"save_user.php", false);
    xmlHttp.setRequestHeader("Content-type", "application/json");
    xmlHttp.send(str_json);
    console.log(xmlHttp.response);

    return 0;
    }*/
<<<<<<< HEAD
=======
  editUser(user: User): Observable<number>{
    this.messageService.add('RecipeService: edit user');
    let param: any = { 'user_edit': JSON.stringify(user) };
    let params = new HttpParams();
    //var userJson = JSON.stringify(user);
    var user_id;
    var result = this.httpCli.get<number>(this.testingURL + "save_user.php", { params: param });
        return result;
        //this.httpCli.get<number>(
        //    this.testingURL + `save_user.php?user_edit=${userJson}`).subscribe();
  }
  newUser(user: User): Observable<number> {
    this.messageService.add('RecipeService: new user');
    let param: any = { 'user': JSON.stringify(user) };
    let params = new HttpParams();
    var user_id;
    var result = this.httpCli.get<number>(this.testingURL + "save_user.php", { params: param });
    return result;
  }
>>>>>>> 702e64b79145587ce28532a09313e768b69d6632

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

  searchTagIconById(tagId, tagList): string {
    if (tagList) {
      for (var i = 0; i < tagList.length; i++) {
        if (tagList[i].id == tagId) {
          return tagList[i].icon;
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
      console.log(recs)
        return recs.find(recipe => recipe.id.toString() == id.toString())
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

  getUserId() {
    return this.userIdSession;
  }

  isUserAllowedToEdit(recipe) {
    console.log(recipe);
    // check if recipe is public for editing
    if (recipe.globalAuthenticationLevel >= 2 || this.getUserLevel() >= 1) {
      return true;
    }
    //check if user created this recipe
    if(recipe.userId == this.getUserId())
    {
      return true;
    }
    // check if user is administrator
    if (this.getUserLevel() >= 2) {
      return true;
    }
    // test if one of users groups is allowed to edit
    if (this.userGroups && recipe.groupsAuthenticationLevel) {
      for (var i = 0; i < this.userGroups.length; i++) {
        for(var j = 0; j < recipe.groupsAuthenticationLevel.length; j++)
        {
          if(this.userGroups[i].toString() == recipe.groupsAuthenticationLevel[j].groupId.toString())
          {
            if(recipe.groupsAuthenticationLevel[j].authenticationLevel.toString() == "2")
            {
              return true;
            }
          }
        }
        
      }
    }

    return false;
  }

  isUserAllowedToCreateRecipe() {
    // if (this.getUserLevel() >= 1)
    //   return true;
    // else
    //   return false;
    return true;
  }

  
}
