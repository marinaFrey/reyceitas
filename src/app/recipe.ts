export class Recipe {
    id: number;
    name: string;
    photos: string[];
    duration: string;
    username: string;
    difficulty: number; // 1 to 5
    servings: number;
    description: string;
    ingredients: Array<Ingredient>;
    preparation: Array<string>;
    tags: Array<number>;
    userId: number;
    globalAuthenticationLevel: number;
    groupsAuthenticationLevel: RecipeVisibility[];
}

export class RecipeView {
    recipe: Recipe;
    isFavourite: boolean;
    isOwned: boolean;
  }

export class Ingredient {
    id: number;
    name: string;
    amount: number;
    unit: string;
}

export class Tag {
    id: number;
    name: string;
    icon: string;
    color: string;
}

export class Group {
    id: number;
    name: string;
}

export class RecipeVisibility {
    groupId: number;
    groupName: string;
    authenticationLevel: number;
}

export class User {
    id: number;
    username: string;
    password: string;
    fullname: string;
    email: string;
    authenticationLevel: number;
    groups: Array<number>;
}

export class ChartFormat {
    labels: string[];
    data: number[];
    colors: string[];
}
