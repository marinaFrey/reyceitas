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
  isEditingUser: Array<boolean>;
  groups: Group[];
  isEditingGroup: Array<boolean>;
  tags: Tag[];
  tagColors: Pickr[];
  isEditingTag: Array<boolean>;


  constructor(private recipeService: RecipeService) { }

  ngOnInit() {
    // checar se usuario eh admin
    var pointer = this;
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
        setTimeout(function () {
          pointer.createColorPickers();
        }, 500);
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
        pointer.tags[args[1].options.index].color = args[0].toHEX().toString();
      });
      console.log(this.tags[i].color, this.tagColors[i].getColor().toHEX().toString());
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
    this.groups.push(g);
    this.isEditingGroup[this.groups.length - 1] = true;
  }

  addNewTag() {
    var t = new Tag();
    t.id = null;
    t.name = null;
    t.color = "white";
    t.icon = null;

    this.tags.push(t);
    var index = (this.tags.length - 1);
    var pointer = this;
    this.isEditingTag[index] = true;
    setTimeout(function () {
      pointer.tagColors[index] = Pickr.create({
        el: '.color-picker'+(index),
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
    }, 500);
  }

  enableUserEditing(index) {
    this.isEditingUser[index] = true;
  }

  enableGroupEditing(index) {
    this.isEditingGroup[index] = true;
  }

  enableTagEditing(index) {
    this.isEditingTag[index] = true;
    this.tagColors[index].enable();
  }

  saveUser(index, userId) {
    this.isEditingUser[index] = false;
    // save user
  }

  saveGroup(index, groupId) {
    this.isEditingGroup[index] = false;
    // save group
  }

  saveTag(index, tagId) {
    this.isEditingTag[index] = false;
    this.tagColors[index].disable();
    // save tag
  }

  setUserPrivilegesCheckboxes(userId) {

  }

  deleteUser(userId) {
    this.createColorPickers();
  }

  deleteGroup(index) {

    if (this.groups[index].id == null) {
      this.groups.splice(index, 1)
    }
  }

  deleteTag(tagId) {

  }

}
