import { Component, OnInit } from '@angular/core';
import { RecipeService } from '../recipe.service';
import { User, Group, Tag } from '../recipe';

@Component({
  selector: 'app-admin-panel',
  templateUrl: './admin-panel.component.html',
  styleUrls: ['./admin-panel.component.css']
})
export class AdminPanelComponent implements OnInit {

  users: User[];
  isEditingUser: Array<boolean>;
  groups: Group[];
  isEditingGroup: Array<boolean>;
  tags: Tag[];
  isEditingTag: Array<boolean>;

  constructor(private recipeService: RecipeService) { }

  ngOnInit() {

    // checar se usuario eh admin

    this.recipeService.getUsers()
      .subscribe(users => { 
        this.users = users;
        this.isEditingUser = this.populateArray(this.users, this.isEditingUser);
      });

    this.recipeService.getGroups()
    .subscribe(groups => { 
      this.groups = groups;
      this.isEditingGroup = this.populateArray(this.groups, this.isEditingGroup);
    });

    this.recipeService.getTags()
    .subscribe(tags => { 
      this.tags = tags;
      this.isEditingTag = this.populateArray(this.tags, this.isEditingTag);
    });

  }

  populateArray(array ,arrayToBePopulated)
  {
    arrayToBePopulated = [];
    for(var i = 0; i < array.length; i++)
    {
      arrayToBePopulated[i] = false;
    }

    return arrayToBePopulated;
  }

  enableUserEditing(index)
  {
    this.isEditingUser[index] = true;
  }

  enableGroupEditing(index)
  {
    this.isEditingGroup[index] = true;
  }

  enableTagEditing(index)
  {
    this.isEditingTag[index] = true;
  }

  saveUser(index, userId)
  {
    this.isEditingUser[index] = false;
    // save user
  }

  saveGroup(index, groupId)
  {
    this.isEditingGroup[index] = false;
    // save user
  }

  saveTag(index, tagId)
  {
    this.isEditingTag[index] = false;
    // save user
  }

  setUserPrivilegesCheckboxes(userId) {

  }

  deleteUser(userId) {

  }

  deleteGroup(groupId) {

  }

  deleteTag(tagId) {

  }

}
