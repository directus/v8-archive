## `@directus/sdk-js` with NGRX

This guide should showcase how you can use the SDK together with the NGRX framework.
If you're not familiar with NGRX (includes Redux and RXJS), we highly recommend checking out [their docs](https://ngrx.io/docs) first.

###### `movies.store.ts`
```ts
import { Action } from '@ngrx/store'

export enum MovieActionTypes {  
    LOAD_ALL = 'MOVIES/LOAD_ALL',
    LOAD_ALL_SUCCESS = 'MOVIES/LOAD_ALL_SUCCESS'
}

export class LoadMovies implements Action {  
    readonly type = MovieActionTypes.LOAD_ALL
    constructor() {}
}

export class LoadMoviesSuccess implements Action {  
    readonly type = MovieActionTypes.LOAD_ALL
    constructor(public payload: Movies[]) {}
}
```
> We're going to re-use our `movie.service.ts` created as described in [this recipe](./Angular.md).

###### `movies.effects.ts`
```ts
@Injectable()
export class MovieEffects {

  @Effect()
  loadMovies$ = this.actions$
    .pipe(
      ofType(MovieActionTypes.LOAD_ALL),
      mergeMap(() => this.moviesService.getAll()
        .pipe(
          map(res => new LoadMoviesSuccess(res.data)),
          catchError(() => EMPTY /* insert your action here */)
        ))
      )
    );
 
  constructor(
    private actions$: Actions,
    private moviesService: MoviesService
  ) {}
}
```
