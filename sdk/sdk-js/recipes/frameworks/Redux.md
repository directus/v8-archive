## `@directus/sdk-js` with Redux

With redux come endless variations on how you can fetch data, most of them are 
quite straight forward. We'll show you a few examples below to get an idea on 
how to work with the SDK inside the redux context. We assume that the following 
API schema is pre-defined:

```ts
// client.ts
export const client = new DirectusSDK({
  url: "https://demo-api.directus.app/",
  email: "admin@example.com",
  password: "password"
});
```

```ts
// movies.store.ts
export interface MoviesState {
  items: Movie[]
}

export const REQUEST_MOVIES = 'MOVIES/REQUEST';
export const REQUEST_MOVIES_SUCCESS = 'MOVIES/REQUEST_SUCCESS';

interface RequestMoviesAction {
  type: typeof REQUEST_MOVIES;
}

interface RequestMoviesSuccessAction {
  type: typeof REQUEST_MOVIES_SUCCESS;
  payload: {
    items: Movie[]
  }
}

// to be continued ...
```

#### Redux Saga
```ts
import { call, put } from 'redux-saga/effects';
import { client } from './client';
import {
  RequestMoviesAction,
  RequestMoviesSuccessAction,
  REQUEST_MOVIES_SUCCESS
} from './movies.store';

export function* loadMovies(action: RequestMoviesAction) {
  const movies = yield call<Movie[]>(client.getItems, 'movies');

  yield put(<RequestMoviesSuccessAction>{
    type: REQUEST_MOVIES_SUCCESS,
    payload: movies.data // Movies[]
  });
}
```

#### Redux Thunk
```ts
import { client } from './client';

function fetchMovies() {
    return client.getItems<Movie[]>('movies');
}

export const getAllMovies = (username: string, password: string): ThunkAction<Promise<void>, RequestMoviesSuccessAction> => {
  return async (dispatch: ThunkDispatch<RequestMoviesSuccessAction>): Promise<void> => {
    const movies = await fetchMovies();

    return new Promise<void>((resolve) => {
        setTimeout(() => {
            dispatch(<RequestMoviesSuccessAction>{
                type: REQUEST_MOVIES_SUCCESS,
                payload: movies.data // Movies[]
            });
        }, 3000)
    });
  }
}
```
