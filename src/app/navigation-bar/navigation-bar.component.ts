import { Component, OnInit } from '@angular/core';
import { RecipeService }  from '../recipe.service';
import { User } from "../recipe";
import { md5 } from "../md5";


@Component({
  selector: 'app-navigation-bar',
  templateUrl: './navigation-bar.component.html',
  styleUrls: ['./navigation-bar.component.css']
})

export class NavigationBarComponent implements OnInit {

    // acho que eh uma variavel que deveria ser meio "global" ao sistema, mas por enquanto vou deixar ela definida aqui
    // para testar a parte do login
    isLoggedIn: boolean;
    searchTerm: string;
    username: string;
    fullname: string;
    email: string;
    password: string;
    passwordHashed: string;

    // Config variables
    usernameConfig: string;
    fullnameConfig: string;
    emailConfig: string;
    oldPasswordConfig: string;
    passwordConfig: string;
    passwordConfirmationConfig: string;

    //Session variables
    usernameSession: string;
    fullnameSession: string;
    emailSession: string;

    user_list: User[];
    new_user: User;


    constructor(private recipeService: RecipeService){}

    ngOnInit() {
    }

    submitLogin(): void
    {
        // usar this.username e this.password para autenticação
        if(!this.username || !this.password)
        {
            console.log("Please inform a valid username and password");
            return;
        }
        this.passwordHashed = md5(this.password);
        this.recipeService.searchUsers(this.username)
            .subscribe(user_list => {
                this.user_list = user_list;
                if(user_list.length > 0 && this.passwordHashed == user_list[0].password)
                {
                    console.log("User "+this.username+" authenticated");
                    this.isLoggedIn = true;
                    this.fullnameSession = user_list[0].fullname;
                    this.emailSession = user_list[0].email;
                    this.usernameSession = user_list[0].username;
                }
            });
            return;
    }
    submitNewUser(): void
    {
        if(!this.usernameConfig || !this.passwordConfig)
        {
            console.log("Please inform a valid username and password");
            return;
        }
        this.passwordHashed = md5(this.passwordConfig);
        this.recipeService.searchUsers(this.usernameConfig)
            .subscribe(user_list => {
                this.user_list = user_list;
                if(this.user_list && this.user_list.length > 0) 
                {
                    console.log("Username already taken");
                    return;
                }
                if(this.passwordConfig == this.passwordConfirmationConfig) 
                {
                    console.log("Password confirmation does not match");
                    return;
                }
                this.new_user = new User();
                this.new_user.username=this.usernameConfig;
                this.new_user.password=this.passwordHashed;
                this.new_user.fullname=this.fullnameConfig;
                this.new_user.email=this.emailConfig;

                this.recipeService.newUser(this.new_user)
                .subscribe(user_id => {
                    this.new_user.id = user_id;
                    if(this.new_user.id != -1)
                    {
                        console.log("User " + this.new_user.id + " created succesfully");
                    }else{
                        console.log("User creation failed");
                    }
                   });
            });

    }

    submitChangesInUser(): void
    {
    console.log(this)
        if(!this.username || !this.password)
        {
            console.log("Please inform a valid username and password");
            return;
        }
        this.passwordHashed = md5(this.password);
        this.recipeService.searchUsers(this.username)
            .subscribe(user_list => {
              this.user_list = user_list;
                if(this.user_list.length > 0 && this.passwordHashed == this.user_list[0].password)
                {
                    console.log("Editing user" + this.user_list[0].id);
                    if(this.passwordConfig == this.passwordConfirmationConfig) 
                    {
                        console.log("Password confirmation does not match");
                        return;
                    }
                    this.new_user = new User();
                    this.new_user.username=this.username;
                    this.new_user.password=this.passwordHashed;
                    this.new_user.fullname=this.fullnameConfig;
                    this.new_user.email=this.emailConfig;

                    this.recipeService.editUser(this.new_user)
                    .subscribe(user_id => {
                        this.new_user.id = user_id;
                        if(this.new_user.id != -1)
                        {
                            console.log("User " + this.new_user.id + " edited succesfully");
                            this.fullnameSession = this.new_user.fullname;
                            this.emailSession = this.new_user.email;
                            this.usernameSession = this.new_user.username;
                        }else{
                            console.log("User edit failed");
                        }
                       });
                    return;
                }else{
                    console.log("User not found");
                    return;
                }
                
            });

    }



}
