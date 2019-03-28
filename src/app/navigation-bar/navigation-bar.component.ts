import { Component, OnInit } from '@angular/core';
import { RecipeService }  from '../recipe.service';
import { User } from "../recipe";


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

    // Config variables
    usernameConfig: string;
    fullnameConfig: string;
    emailConfig: string;
    oldPasswordConfig: string;
    passwordConfig: string;
    passwordConfirmationConfig: string;

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
        this.recipeService.searchUsers(this.username)
            .subscribe(user_list => {
                this.user_list = user_list;
                if(user_list.length > 0 && this.password == user_list[0].password)
                {
                    console.log("User "+this.username+" authenticated");
                    this.isLoggedIn = true;
                    this.fullname = user_list[0].fullName;
                    this.email = user_list[0].email;
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
        this.recipeService.searchUsers(this.usernameConfig)
            .subscribe(user_list => {
                this.user_list = user_list;
                if(this.user_list && this.user_list.length > 0) 
                {
                    console.log("Username already taken");
                    return;
                }
                this.new_user = new User();
                this.new_user.username=this.usernameConfig;
                this.new_user.password=this.passwordConfig;
                this.new_user.fullName=this.fullnameConfig;
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
        if(!this.usernameConfig || !this.passwordConfig)
        {
            console.log("Please inform a valid username and password");
            return;
        }
        this.recipeService.searchUsers(this.usernameConfig)
            .subscribe(user_list => {
              this.user_list = user_list;
                if(this.user_list.length > 0 && this.password == this.user_list[0].password)
                {
                    console.log("Editing user" + this.user_list[0].id);
                    return;
                }
            });

        this.new_user = new User();
        this.new_user.username=this.usernameConfig;
        this.new_user.password=this.passwordConfig;
        this.new_user.fullName=this.fullnameConfig;
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
    }



}
