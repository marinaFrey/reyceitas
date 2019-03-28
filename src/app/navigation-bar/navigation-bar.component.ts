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

  // chamar aqui funções de autenticação de login
  submitLogin(): void
  {
    // usar this.username e this.password para autenticação
    console.log(this.username,this.password);
    this.isLoggedIn = true;
  }
  submitChangesInUser(): void
  {
    console.log(this.usernameConfig,this.passwordConfig, this.fullnameConfig, this.emailConfig, this.oldPasswordConfig, this.passwordConfirmationConfig);
    this.recipeService.searchUsers("testudo")
        .subscribe(user_list => {
          this.user_list = user_list;
        });

    this.new_user = new User();
    this.new_user.id=3;
    this.new_user.username="Testo";
    this.new_user.password="to";
    this.new_user.fullName="tsadsd";
    this.new_user.email="hax00r69@bol.com.br";

    this.recipeService.newUser(this.new_user);
  }



}
