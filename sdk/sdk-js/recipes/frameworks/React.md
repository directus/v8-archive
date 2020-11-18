## `@directus/sdk-js` with React

### With hooks (recommended)

> Live demo available via [stackblitz](https://stackblitz.com/edit/directus-react-hooks?file=index.tsx)

By relying on hooks, you're able to import a function which manages your response,
state, errors and loading-state handling, all in one line:

```tsx
import React from 'react';
import { render } from 'react-dom';
import { useDirectus } from './useDirectus';
import { wait } from './utils';

const App: React.FC<{}> = () => {
  const [result, error, state] = useDirectus(async client => {
    await wait(1000);
    return await client.ping();
  });

  if(state === 'loading') {
    return <h1>Loading ...</h1>
  }

  if(state === 'errored') {
    return <h1 style={{color: 'red'}}>{error.message}</h1>
  }
  
  // will render <h1>pong</h1> after 1s
  return <h1>{result}</h1>
}
```

In order to make this happen, we'll have to setup the useDirectus hook function separately. The snippet below should be all you need to get started:

```tsx
import { useState, useEffect, useDebugValue } from 'react';
import DirectusSDK from '@directus/sdk-js';

// main client instance
const client = new DirectusSDK({
  url: 'https://demo-api.directus.app/'
});

// define the loading states
export enum ResponseState {
  LOADING = 'loading',
  ERRORED = 'errored',
  SUCCESS = 'success'
}

export function useDirectus<
  T extends any = any,
  O extends any = undefined
>(fn: (client: DirectusSDK, opts?: O) => Promise<T>, opts?: O): [T, Error | undefined, ResponseState] {
  // save error and response state internally
  const [response, setResponse] = useState<T | undefined>();
  const [error, setError] = useState<Error | undefined>();

  useEffect(() => {
    // will call the API with your provided fn
    const fetcher = async () => {
      try {
        const result = await fn(client);
        // set the response if everything's allright
        setResponse(result);
      } catch (err) {
        // set the error if something went wrong
        setError(err);
      }
    }

    // execute!
    fetcher();
  }, [])

  return [
    response,
    error,
    error
      ? ResponseState.ERRORED
      : response
        ? ResponseState.SUCCESS
        : ResponseState.LOADING
    ];
}
```

### With higher order components (HoC)

Alternatively, the SDK can also be used as a higher-order component (HoC). This allows you to directly bind API states to a component. While this is a clean way to inject data from Directus into a component, it doesn't scale very well into large applications. For larger applications, we recommend to use a state paradimg like [Redux](./Redux) or similar.

```tsx
import React, { Component } from 'react';
import SDK from '@directus/sdk-js';

const client = new DirectusSDK({
    // ...
});

interface IWithItemsState<T> {
  items: T[];
  error?: Error;
}

export function withItems<T, P extends object = {}>(
  Component: React.ComponentType<P>,
  select: string,
) {
    return class WithItems extends Component<P, IWithItemsState<T>> {

    public state: IWithItemsState = {
      items: [],
      error: undefined
    };

    public componentWillMount() {
        this.fetchItems();
    }

    public render() {
      return (
        <Component
          {...this.props as P}
          items={this.state.items}
          error={this.state.error}
        />
      );
    }

    private async fetchItems() {
        // TODO: Introduce cache mechanism etc.
        try {
            const items = await client.getItems(select);
            if(items.length > 0) {
                this.setState({ items });
            }
        } catch (error) {
            this.setState({ error })
        }
    }
  }
};
```

Then we can use it like this:

```tsx
import * as React from 'react';
import { withItems } from './withItems';

interface IMoviesProps {
    items: Movie[];
}

const Movies = ({ items }: IMoviesProps) => (
    <ul>
        {items.map((movie: Movie) => {
            <li key={movie.id}>{movie.title} (${movie.duration})</li>
        })}
    </ul>
)

export default withItems<Movie[], IMoviesProps>(Movies);
```
