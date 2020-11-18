## `@directus/sdk-js` with Angular 2+

We want to take a look on how to provide the API layer as a generic service.
This service provides the main access to the SDK. It will be used in other
services as reference to the API, which is the classic Angular approach.

> Important: do not forget to add injectable services to the providers list of your application module! A fully working and editable example can be found on [stackblitz](https://stackblitz.com/edit/directus-angular-service?file=src/app/app.component.ts).

* If you're interested in using the SDK together with NGRX, please head over to the 
[NGRX](./NGRX.md) section. 

* If you're using Angular together with plain RXJS we'd suggest you to take a look at 
the [RXJS](./RXJS.md) recipe.

```ts
import { Injectable } from '@angular/core';
import DirectusSDK from "@directus/sdk-js";

@Injectable({
  providedIn: 'root',
})
export class DirectusService {
  private internalSDKClient = new DirectusSDK({
    // ... auth here
  });

  public get api(): DirectusSDK {
    return this.internalSDKClient;
  }
}
```

Next up we create services for each table which can be used within e.g. a
store layer or something similar.

###### `ping.service.ts`
```ts
import { Injectable } from '@angular/core';
import { DirectusService } from './directus.service';

@Injectable({
  providedIn: 'root',
})
export class PingService {
  constructor(private directusService: DirectusService) {}

  public async send(): Promise<string> {
    return await this.directusService.api.ping();
  }
}
```

And now we can use this service in components etc. as you'd like to use it.

###### `app.component.ts`
```ts
import { Component, OnInit } from '@angular/core';
import { PingService } from './ping.service';

@Component({
  selector: 'my-app',
  templateUrl: './app.component.html',
  styleUrls: [ './app.component.css' ],
  providers: [ PingService ]
})
export class AppComponent implements OnInit {
  name = '...';

  constructor(private pingService: PingService) {}

  public async ngOnInit() {
    this.name = await this.pingService.send();
  }
}
```
