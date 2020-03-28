import { Component, OnInit } from '@angular/core';
import { RecipeService } from '../recipe.service';
import { UserService } from '../user.service';
import { User, Group, Tag } from '../recipe';
//import '@simonwep/pickr/dist/pickr.min.css';
//import Pickr from '@simonwep/pickr/dist/pickr.min';

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
  //@ts-ignore
  tagColors: Pickr[];
  isEditingTag: Array<boolean>;


  constructor(private recipeService: RecipeService, private userService : UserService) { }

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
        theme: 'classic',

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
        pointer.tagsTemp[args[1].options.index].color = args[0].toHEXA().toString();
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
    this.tags.push(t);
    var index = (this.tagsTemp.length - 1);
    var pointer = this;
    this.isEditingTag[index] = true;
    setTimeout(function () {
      pointer.tagColors[index] = Pickr.create({
        el: '.color-picker' + (index),
        default: "grey",
        index: index,
        theme: 'classic',
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
        pointer.tagsTemp[args[1].options.index].color = args[0].toHEXA().toString();
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
    this.recipeService.getUserGroups(this.users[this.userGroupEditingIndexReference].id).subscribe(usrGroups => {
      var userGroups = usrGroups;
      for (var i = 0; i < this.groups.length; i++) {
        var participationCheckbox = (<HTMLInputElement>document.getElementById('participation' + this.groups[i].id));
        participationCheckbox.checked = false;
        if (userGroups && userGroups.length > 0) {
          for (var j = 0; j < userGroups.length; j++) {
            if (this.groups[i].id.toString() == userGroups[j].toString())
              participationCheckbox.checked = true;
          }
        }

      }
    });


  }

  selectAllGroupsParticipationCheckboxes(allId) {
    var participationCheckbox = (<HTMLInputElement>document.getElementById(allId));
    var value = false;
    if (participationCheckbox.checked)
      value = true;
    for (var i = 0; i < this.groups.length; i++) {
      var participationCheckbox = (<HTMLInputElement>document.getElementById('participation' + this.groups[i].id));
      participationCheckbox.checked = value;
    }

  }

  enableGroupEditing(index) {
    this.isEditingGroup[index] = true;
  }

  cancelGroupEditing(index) {
    if (this.groupsTemp[index].id == null) {
      this.groups.splice(index, 1);
      this.groupsTemp.splice(index, 1);
      this.isEditingGroup.splice(index, 1);
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
    console.log("Saving User: ")
    console.log(this.users[index])
    this.userService.editUser(this.users[index]);
                    this.userService.editUser(this.users[index])
                        .subscribe(user_id => {
                            this.users[index].id = user_id;
                            if (this.users[index].id != -1) {
                                console.log("User " + this.users[index].id + " edited succesfully");
                            } else {
                                console.log("User edit failed");
                            }
                        });

    // save user

  }

  saveUserGroups() {
    console.log(this.groups, this.users[this.userGroupEditingIndexReference])
    var userGroups = [];
    for (var i = 0; i < this.groups.length; i++) {
      var participationCheckbox = (<HTMLInputElement>document.getElementById('participation' + this.groups[i].id));
      if (participationCheckbox.checked)
        userGroups.push(this.groups[i].id);
    }
    this.recipeService.setUserGroups(this.users[this.userGroupEditingIndexReference].id, JSON.stringify(userGroups));
    console.log("changing groups for user " + this.users[this.userGroupEditingIndexReference].username);
    console.log(userGroups);
  }

  saveGroup(index, group) {
    this.isEditingGroup[index] = false;
    this.groups[index] = JSON.parse(JSON.stringify(this.groupsTemp[index]));
    // save group
    if (this.groups[index].id == null) {
      this.recipeService.addGroup(this.groups[index]).subscribe(id => this.groups[index].id = id);
    }
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
    if (this.groups[index].id == null) {
      this.groups.splice(index, 1)
    } else {
      this.recipeService.rmGroup(this.groups[index].id);
    }
    this.groups.splice(index, 1);
    this.groupsTemp.splice(index, 1);
    this.isEditingGroup.splice(index, 1);

  }

  deleteTag(index, tagId) {
    this.tags.splice(index, 1);
    this.tagsTemp.splice(index, 1);
    this.isEditingTag.splice(index, 1);
    this.recipeService.rmTag(tagId);

  }

}
