import { Injectable } from '@angular/core';
import { User } from './recipe';
import { MessageService } from './message.service';
import { HttpClient, HttpParams, HttpHeaders } from '@angular/common/http';
import { RequestOptions, ResponseContentType } from '@angular/http';
import { Observable, empty, of } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UserService {

  distURL = "https://receitas.fortesrey.net/backend/";
  testingURL = "http://localhost:8000/";

  public currentUser: User;
  public logged_user_observable: Observable<User>;
  public isLoggedIn: boolean;

  constructor(private messageService: MessageService, private httpCli: HttpClient) {
    this.isLoggedIn = false;
    this.loginFromCookie();
  }

  getCurretUser(): Observable<User> {
    console.log("hey");
    console.log(this.logged_user_observable);
    return this.logged_user_observable;
  }

  loginFromCookie() {
    console.log("Startup");
    if(document.cookie.indexOf(';ME=') != -1 || document.cookie.indexOf('; ME=') != -1 
        ||  document.cookie.indexOf('ME=') == 0) 
    {
      const httpOptions =  { 
        headers: new HttpHeaders({
          'Content-Type':  'application/x-www-form-urlencoded',
          'Accept': 'application/json',
        }),
        withCredentials : true
      };


      this.logged_user_observable = this.httpCli.get<User>(this.testingURL + "login.php?cookie=1", httpOptions);
      this.logged_user_observable.subscribe(
            user => {
              this.currentUser = user;
              this.isLoggedIn = true;
              console.log("Automatic");
              console.log(user);
            },
            error => {
              // Remove cookie.
              this.currentUser = null;
              console.log("Error");
              console.log(error);
            });
    } else {
      this.currentUser = null;
      // this.logged_user_observable = empty();
      const httpOptions =  { 
        headers: new HttpHeaders({
          'Content-Type':  'application/x-www-form-urlencoded',
          'Accept': 'application/json',
        }),
        withCredentials : true
      };
      this.logged_user_observable = this.httpCli.get<User>(this.testingURL + "login.php?cookie=1", httpOptions);
    }
  }



  login(username, password) {

    this.messageService.add('UserService: login');
    var creds = JSON.stringify(
       {'username' : username, 'password' : password} );
    var str_json = creds;

    const httpOptions =  {
      // params : my_params,//new HttpParams().set('credentials', str_json),
      headers: new HttpHeaders({
        'Content-Type':  'application/x-www-form-urlencoded',
        'Accept': 'application/json',
        'Authorization': username + ":" + password,
      }),
      withCredentials : true
    };

    var user = this.httpCli.post<User>(this.testingURL + 'login.php',
      'credentials=' + str_json 
      + '&grant_type=client_credentials&client_id='+username
      +'&client_secret='+ password,
      httpOptions);

    this.logged_user_observable = user;
    return user;
  }

  newUser(user: User): Observable<number> {
    this.messageService.add('RecipeService: new user');
    let param: any = { 'user': JSON.stringify(user) };
    let params = new HttpParams();
    var user_id = this.httpCli.get<number>(this.testingURL + "save_user.php", { params: param });
    return user_id;
  }

  logout() {
    console.log("LOGOUT");
    // document.cookie = 'ME=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    document.cookie = 'ME=; Max-Age=-99999999;';
    this.isLoggedIn = false;
  }

  editUser(user: User): Observable<number> {
    this.messageService.add('UserService: edit user');
    let param: any = { 'user_edit': JSON.stringify(user) };
    let params = new HttpParams();
    var user_id;
    var result = this.httpCli.get<number>(this.testingURL + "save_user.php", { params: param });
    return result;
  }

  getUsernameById(id): Observable<string> {
    this.messageService.add('RecipeService: fetched users');

    var username = this.httpCli.get<string>(
      this.testingURL + `get_users.php?id=${id}`);
    console.log(username)
    return username;
  }

  getUsers(): Observable<User[]> {
    this.messageService.add('RecipeService: fetched users');

    var users = this.httpCli.get<User[]>(
      this.testingURL + "get_users.php");
    return users;
  }


}
