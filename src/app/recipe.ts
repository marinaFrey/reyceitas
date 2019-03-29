export class Recipe {
    id: number;
    name: string;
    photos: string[];
    duration: string;
    difficulty: number; // 1 to 5
    servings: number;
    description: string;
    ingredients: Array<Ingredient>;
    preparation: Array<string>;
    tags: Array<number>;
    userId: number;
    authenticationLevelRequired: number;
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

export class User {
    id: number;
    username: string;
    password: string;
    fullname: string;
    email: string;
}

export class ChartFormat {
    labels: string[];
    data: number[];
    colors: string[];
}
