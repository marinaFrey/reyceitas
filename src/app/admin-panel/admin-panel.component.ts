import { Component, OnInit } from '@angular/core';
import { RecipeService } from '../recipe.service';
import { User, Group, Tag } from '../recipe';
import Pickr from '@simonwep/pickr/dist/pickr.min.js';

@Component({
  selector: 'app-admin-panel',
  templateUrl: './admin-panel.component.html',
  styleUrls: ['./admin-panel.component.css']
})
export class AdminPanelComponent implements OnInit {

  users: User[];
  usersTemp: User[];
  userGroupEditingIndexReference: number;
  isEditingUser: Array<boolean>;
  groups: Group[];
  groupsTemp: Group[];
  isEditingGroup: Array<boolean>;
  tags: Tag[];
  tagsTemp: Tag[];
  tagColors: Pickr[];
  isEditingTag: Array<boolean>;


  constructor(private recipeService: RecipeService) { }

  ngOnInit() {
    // checar se usuario eh admin

    this.getUsersFromDatabase();
    this.getGroupsFromDatabase();
    this.getTagsFromDatabase();
  }

  getUsersFromDatabase() {
    this.recipeService.getUsers()
      .subscribe(users => {
        this.users = users;
        this.usersTemp = JSON.parse(JSON.stringify(this.users));//this.users.slice();
        this.isEditingUser = this.populateArray(this.users, this.isEditingUser);
      });

  }

  getGroupsFromDatabase() {
    this.recipeService.getGroups()
      .subscribe(groups => {
        this.groups = groups;
        this.groupsTemp = JSON.parse(JSON.stringify(this.groups));//this.groups.slice();
        this.isEditingGroup = this.populateArray(this.groups, this.isEditingGroup);
      });
  }

  getTagsFromDatabase() {
    var pointer = this;
    this.recipeService.getTags()
      .subscribe(tags => {
        this.tags = tags;
        this.tagsTemp = JSON.parse(JSON.stringify(this.tags));//this.tags.slice();
        this.isEditingTag = this.populateArray(this.tags, this.isEditingTag);
        setTimeout(function () {
          pointer.createColorPickers();
        }, 10);
      });
  }

  createColorPickers() {
    this.tagColors = [];
    for (var i = 0; i < this.tags.length; i++) {
      var elem = '.color-picker' + i;
      this.tagColors[i] = Pickr.create({
        el: elem,
        default: this.tags[i].color,
        index: i,
        components: {
          // Main components
          preview: true,
          opacity: true,
          hue: true,

          // Input / output Options
          interaction: {
            hex: true,
            rgba: true,
            hsla: true,
            hsva: true,
            cmyk: true,
            input: true,
            clear: true,
            save: true
          }
        }
      });
      this.tagColors[i].disable();
      var pointer = this;
      this.tagColors[i].on('save', (...args) => {
        pointer.tagsTemp[args[1].options.index].color = args[0].toHEX().toString();
        //pointer.tags[args[1].options.index].color = args[0].toHEX().toString();
      });
    }
  }

  populateArray(array, arrayToBePopulated) {
    arrayToBePopulated = [];
    for (var i = 0; i < array.length; i++) {
      arrayToBePopulated[i] = false;
    }

    return arrayToBePopulated;
  }

  addNewGroup() {
    var g = new Group();
    g.id = null;
    g.name = "Novo Grupo"
    this.groupsTemp.push(g);
    this.groups.push(g);
    this.isEditingGroup[this.groupsTemp.length - 1] = true;
  }

  addNewTag() {
    var t = new Tag();
    t.id = null;
    t.name = null;
    t.color = "grey";
    t.icon = null;

    this.tagsTemp.push(t);
    var index = (this.tagsTemp.length - 1);
    var pointer = this;
    this.isEditingTag[index] = true;
    setTimeout(function () {
      pointer.tagColors[index] = Pickr.create({
        el: '.color-picker' + (index),
        default: "grey",
        index: index,
        components: {
          // Main components
          preview: true,
          opacity: true,
          hue: true,

          // Input / output Options
          interaction: {
            hex: true,
            rgba: true,
            hsla: true,
            hsva: true,
            cmyk: true,
            input: true,
            clear: true,
            save: true
          }
        }
      });
      pointer.tagColors[index].on('save', (...args) => {
        pointer.tagsTemp[args[1].options.index].color = args[0].toHEX().toString();
        //pointer.tags[args[1].options.index].color = args[0].toHEX().toString();
      });
    }, 10);
  }

  enableUserEditing(index) {
    this.isEditingUser[index] = true;
  }

  cancelUserEditing(index) {
    this.isEditingUser[index] = false;
    this.usersTemp[index] = JSON.parse(JSON.stringify(this.users[index]));
  }

  openUserGroupsEditingModal(index) {
    this.userGroupEditingIndexReference = index;
    $('#groupManagingModal').modal('show');
    var pointer = this;
    setTimeout(function () {
      for (var i = 0; i < pointer.groups.length; i++) {
        var participationCheckbox = (<HTMLInputElement>document.getElementById('participation' + pointer.groups[i].id));
        participationCheckbox.checked = false;
      }
    }, 10);

  }

  enableGroupEditing(index) {
    this.isEditingGroup[index] = true;
  }

  cancelGroupEditing(index) {
    if (this.groupsTemp[index].id == null) {
      this.groups.splice(index, 1);
      this.groupsTemp.splice(index, 1);
      return;
    }
    this.isEditingGroup[index] = false;
    this.groupsTemp[index] = JSON.parse(JSON.stringify(this.groups[index]));
    
  }

  enableTagEditing(index) {
    this.isEditingTag[index] = true;
    this.tagColors[index].enable();
  }

  cancelTagEditing(index) {
    this.isEditingTag[index] = false;

    var copy = JSON.parse(JSON.stringify(this.tags[index]));
    this.tagsTemp[index].id = copy.id;
    this.tagsTemp[index].name = copy.name;
    this.tagsTemp[index].icon = copy.icon;
    this.tagsTemp[index].color = copy.color;
    this.tagColors[index].setColor(copy.color);
    this.tagColors[index].disable();

  }

  saveUser(index, user) {
    this.isEditingUser[index] = false;
    this.users[index] = JSON.parse(JSON.stringify(this.usersTemp[index]));
    // save user

  }

  saveUserGroups() {
    for (var i = 0; i < this.groups.length; i++) {
      var participationCheckbox = (<HTMLInputElement>document.getElementById('participation' + i));
    }
    console.log("changing groups for user " + this.users[this.userGroupEditingIndexReference].username);
  }

  saveGroup(index, group) {
    this.isEditingGroup[index] = false;
    this.groups[index] = JSON.parse(JSON.stringify(this.groupsTemp[index]));
    // save group
    if (this.groups[index].id == null)
      this.recipeService.addGroup(this.groups[index]);
    else
      this.recipeService.editGroup(this.groups[index]);

  }

  saveTag(index, tag) {
    this.isEditingTag[index] = false;
    this.tagColors[index].disable();
    this.tags[index] = JSON.parse(JSON.stringify(this.tagsTemp[index]));
    if (this.tags[index].id == null)
      this.recipeService.addTag(tag);
    else
      this.recipeService.editTag(tag);

  }

  setUserPrivilegesCheckboxes(userId) {

  }

  deleteUser(index, userId) {
    this.createColorPickers();
  }

  deleteGroup(index) {

    this.groups.splice(index, 1);
    this.groupsTemp.splice(index, 1);

    if (this.groups[index].id == null) {
      this.groups.splice(index, 1)
    } else {
      this.recipeService.rmGroup(this.groups[index].id);
    }

  }

  deleteTag(index, tagId) {
    this.tags.splice(index, 1);
    this.tagsTemp.splice(index, 1);
    this.recipeService.rmTag(tagId);

  }

}
