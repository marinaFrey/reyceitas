import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms'; // <-- NgModel lives here
import { AngularFontAwesomeModule } from 'angular-font-awesome';
import { AlertModule } from '../../node_modules/ngx-bootstrap';

import { AppComponent } from './app.component';
import { RecipesComponent } from './recipes/recipes.component';
import { HeaderComponent } from './header/header.component';
import { FooterComponent } from './footer/footer.component';
import { RecipeThumbnailComponent } from './recipes/recipe-thumbnail/recipe-thumbnail.component';
import { RecipeDetailsComponent } from './recipes/recipe-details/recipe-details.component';
import { RecipeService } from './recipe.service';
import { AppRoutingModule } from './/app-routing.module';
import { DashboardComponent } from './dashboard/dashboard.component';
import { MessagesComponent } from './messages/messages.component';
import { NavigationBarComponent } from './navigation-bar/navigation-bar.component';
import { AdvancedSearchComponent } from './advanced-search/advanced-search.component';
import {HttpClientModule} from '@angular/common/http';


@NgModule({
  declarations: [
    AppComponent,
    RecipesComponent,
    HeaderComponent,
    FooterComponent,
    RecipeThumbnailComponent,
    RecipeDetailsComponent,
    DashboardComponent,
    MessagesComponent,
    NavigationBarComponent,
    AdvancedSearchComponent
  ],
  imports: [
    AlertModule.forRoot(),
    BrowserModule,
    FormsModule,
    AngularFontAwesomeModule,
    AppRoutingModule,
    HttpClientModule
  ],
  
  providers: [RecipeService],
  bootstrap: [AppComponent]
})
export class AppModule { }
