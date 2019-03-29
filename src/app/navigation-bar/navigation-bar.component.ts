import { Component, OnInit } from '@angular/core';
import { RecipeService } from '../recipe.service';
import { User } from "../recipe";
import { md5 } from "../md5";
import * as $AB from 'jquery';
import * as bootstrap from "bootstrap";


@Component({
    selector: 'app-navigation-bar',
    templateUrl: './navigation-bar.component.html',
    styleUrls: ['./navigation-bar.component.css']
})

export class NavigationBarComponent implements OnInit
{

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


    constructor(private recipeService: RecipeService) { }

    ngOnInit()
    {
    }

    checkField(field,elementId): number
    {
        if(!field)
        {
            var element = document.getElementById(elementId);
            element.classList.add("is-invalid");
            return 0;
        }
        else
        {
            var element = document.getElementById(elementId);
            element.classList.remove("is-invalid");
            return 1;
        }
    }
    submitLogin(): void
    {
        // usar this.username e this.password para autenticação
        if (!this.checkField(this.username, "loginUsername") || !this.checkField(this.password, "loginPassword"))
            return;

        this.passwordHashed = md5(this.password.toString());
        this.password = null;
        this.recipeService.searchUsers(this.username)
            .subscribe(user_list =>
            {
                this.user_list = user_list;
                if (this.user_list && this.user_list.length > 0) 
                {
                    if (this.passwordHashed != this.user_list[0].password)
                    {
                        var element = document.getElementById("loginPassword");
                        element.classList.add("is-invalid");
                        console.log("Password does not match");
                        return;
                    }
                    $('#loginModal').modal('hide');
                    $('#loginModal .close').click();
                    var element = document.getElementById("loginUsername");
                    element.classList.remove("is-invalid");
                    var element = document.getElementById("loginPassword");
                    element.classList.remove("is-invalid");
                    console.log("User " + this.username + " authenticated");
                    this.isLoggedIn = true;
                    this.fullnameSession = user_list[0].fullname;
                    this.emailSession = user_list[0].email;
                    this.usernameSession = user_list[0].username;

                } else
                {
                    var element = document.getElementById("loginUsername");
                    element.classList.add("is-invalid");
                    console.log("User not found");
                    return;
                }
            });
        return;
    }
    submitNewUser(): void
    {
        if (!this.checkField(this.usernameConfig, "usernameConfig") 
        || !this.checkField(this.fullnameConfig, "fullnameConfig")
        || !this.checkField(this.emailConfig, "emailConfig")
        || !this.checkField(this.passwordConfig, "passwordConfig")
        )
        {
            return;
        }
        this.passwordHashed = md5(this.passwordConfig.toString());
        this.recipeService.searchUsers(this.usernameConfig)
            .subscribe(user_list =>
            {
                this.user_list = user_list;
                if (this.user_list && this.user_list.length > 0) 
                {
                    var element = document.getElementById("usernameConfig");
                    element.classList.add("is-invalid");
                    console.log("Username already taken");
                    return;
                }
                if (this.passwordConfig != this.passwordConfirmationConfig) 
                {
                    var element = document.getElementById("passwordConfirmationConfig");
                    element.classList.add("is-invalid");
                    this.passwordConfig = null;
                    this.passwordConfirmationConfig = null;
                    return;
                }
                this.passwordConfig = null;
                this.passwordConfirmationConfig = null;
                this.new_user = new User();
                this.new_user.username = this.usernameConfig;
                this.new_user.password = this.passwordHashed;
                this.new_user.fullname = this.fullnameConfig;
                this.new_user.email = this.emailConfig;

                $('#loginConfigurationModal').modal('hide');
                $('#loginConfigurationModal .close').click();
                //$('#loginModal').modal('hide');
                //$('#loginModal .close').click();
                this.recipeService.newUser(this.new_user)
                    .subscribe(user_id =>
                    {
                        this.new_user.id = user_id;
                        if (this.new_user.id != -1)
                        {
                            console.log("User " + this.new_user.id + " created succesfully");
                        } else
                        {
                            console.log("User creation failed");
                        }
                    });
            });
    }
    submitChangesInUser(): void
    {
        if (!this.checkField(this.usernameConfig, "usernameConfig") 
        || !this.checkField(this.fullnameConfig, "fullnameConfig")
        || !this.checkField(this.emailConfig, "emailConfig")
        || !this.checkField(this.passwordConfig, "passwordConfig")
        )
        {
            return;
        }
        this.passwordHashed = md5(this.oldPasswordConfig.toString());
        this.oldPasswordConfig = null;
        this.recipeService.searchUsers(this.username)
            .subscribe(user_list =>
            {
                this.user_list = user_list;
                if (this.user_list.length > 0) 
                {
                    if (this.passwordHashed != this.user_list[0].password)
                    {
                        var element = document.getElementById("oldPasswordConfig");
                        element.classList.add("is-invalid");
                        console.log("Password does not match");
                        return;
                    }
                    console.log("Editing user" + this.user_list[0].id);
                    if (this.passwordConfig != this.passwordConfirmationConfig) 
                    {
                        var element = document.getElementById("passwordConfirmationConfig");
                        element.classList.add("is-invalid");
                        this.passwordConfig = null;
                        this.passwordConfirmationConfig = null;
                        console.log("Password confirmation does not match");
                        return;
                    }
                    this.passwordConfig = null;
                    this.passwordConfirmationConfig = null;
                    this.new_user = new User();
                    this.new_user.username = this.username;
                    this.new_user.password = this.passwordHashed;
                    this.new_user.fullname = this.fullnameConfig;
                    this.new_user.email = this.emailConfig;
                    $('#loginConfigurationModal').modal('hide');
                    $('#loginConfigurationModal .close').click();

                    this.recipeService.editUser(this.new_user)
                        .subscribe(user_id =>
                        {
                            this.new_user.id = user_id;
                            if (this.new_user.id != -1)
                            {
                                console.log("User " + this.new_user.id + " edited succesfully");
                                this.fullnameSession = this.new_user.fullname;
                                this.emailSession = this.new_user.email;
                                this.usernameSession = this.new_user.username;
                            } else
                            {
                                console.log("User edit failed");
                            }
                        });
                    return;
                } else
                {
                    console.log("User not found");
                    return;
                }

            });
    }
    
}
