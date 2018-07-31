import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RecipeThumbnailComponent } from './recipe-thumbnail.component';

describe('RecipeThumbnailComponent', () => {
  let component: RecipeThumbnailComponent;
  let fixture: ComponentFixture<RecipeThumbnailComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RecipeThumbnailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RecipeThumbnailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
