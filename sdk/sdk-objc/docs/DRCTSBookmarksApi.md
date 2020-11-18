# DRCTSBookmarksApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**addBookmark**](DRCTSBookmarksApi.md#addbookmark) | **POST** /bookmarks | Create a column in a given table
[**deleteBookmark**](DRCTSBookmarksApi.md#deletebookmark) | **DELETE** /bookmarks/{bookmarkId} | Deletes specific bookmark
[**getBookmark**](DRCTSBookmarksApi.md#getbookmark) | **GET** /bookmarks/{bookmarkId} | Returns specific bookmark
[**getBookmarks**](DRCTSBookmarksApi.md#getbookmarks) | **GET** /bookmarks | Returns bookmarks
[**getBookmarksSelf**](DRCTSBookmarksApi.md#getbookmarksself) | **GET** /bookmarks/self | Returns bookmarks of current user


# **addBookmark**
```objc
-(NSURLSessionTask*) addBookmarkWithUser: (NSString*) user
    title: (NSString*) title
    url: (NSString*) url
    iconClass: (NSString*) iconClass
    active: (NSString*) active
    section: (NSString*) section
        completionHandler: (void (^)(NSError* error)) handler;
```

Create a column in a given table

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* user = @"user_example"; // [Directus user id] This assigns the bookmark to a specific user (there's a ticket to allow for \"global\" bookmarks using NULL) (Only using local connection) (optional)
NSString* title = @"title_example"; // The text to display in the navigation menu (optional)
NSString* url = @"url_example"; // The path to navigate to when clicked, relative to the Directus root (optional)
NSString* iconClass = @"iconClass_example"; // Deprecated (optional)
NSString* active = @"active_example"; // Deprecated (optional)
NSString* section = @"section_example"; // [\"search\" or \"other\"] Which nav section to show the link within. User generated bookmarks use \"search\", while all system links go within \"other\" (optional)

DRCTSBookmarksApi*apiInstance = [[DRCTSBookmarksApi alloc] init];

// Create a column in a given table
[apiInstance addBookmarkWithUser:user
              title:title
              url:url
              iconClass:iconClass
              active:active
              section:section
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSBookmarksApi->addBookmark: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **user** | **NSString***| [Directus user id] This assigns the bookmark to a specific user (there&#39;s a ticket to allow for \&quot;global\&quot; bookmarks using NULL) (Only using local connection) | [optional] 
 **title** | **NSString***| The text to display in the navigation menu | [optional] 
 **url** | **NSString***| The path to navigate to when clicked, relative to the Directus root | [optional] 
 **iconClass** | **NSString***| Deprecated | [optional] 
 **active** | **NSString***| Deprecated | [optional] 
 **section** | **NSString***| [\&quot;search\&quot; or \&quot;other\&quot;] Which nav section to show the link within. User generated bookmarks use \&quot;search\&quot;, while all system links go within \&quot;other\&quot; | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **deleteBookmark**
```objc
-(NSURLSessionTask*) deleteBookmarkWithBookmarkId: (NSNumber*) bookmarkId
        completionHandler: (void (^)(NSError* error)) handler;
```

Deletes specific bookmark

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSNumber* bookmarkId = @56; // ID of table to return rows from

DRCTSBookmarksApi*apiInstance = [[DRCTSBookmarksApi alloc] init];

// Deletes specific bookmark
[apiInstance deleteBookmarkWithBookmarkId:bookmarkId
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSBookmarksApi->deleteBookmark: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **bookmarkId** | **NSNumber***| ID of table to return rows from | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getBookmark**
```objc
-(NSURLSessionTask*) getBookmarkWithBookmarkId: (NSNumber*) bookmarkId
        completionHandler: (void (^)(DRCTSGetBookmark* output, NSError* error)) handler;
```

Returns specific bookmark

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSNumber* bookmarkId = @56; // ID of table to return rows from

DRCTSBookmarksApi*apiInstance = [[DRCTSBookmarksApi alloc] init];

// Returns specific bookmark
[apiInstance getBookmarkWithBookmarkId:bookmarkId
          completionHandler: ^(DRCTSGetBookmark* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSBookmarksApi->getBookmark: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **bookmarkId** | **NSNumber***| ID of table to return rows from | 

### Return type

[**DRCTSGetBookmark***](DRCTSGetBookmark.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getBookmarks**
```objc
-(NSURLSessionTask*) getBookmarksWithCompletionHandler: 
        (void (^)(DRCTSGetBookmarks* output, NSError* error)) handler;
```

Returns bookmarks

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];



DRCTSBookmarksApi*apiInstance = [[DRCTSBookmarksApi alloc] init];

// Returns bookmarks
[apiInstance getBookmarksWithCompletionHandler: 
          ^(DRCTSGetBookmarks* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSBookmarksApi->getBookmarks: %@", error);
                        }
                    }];
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**DRCTSGetBookmarks***](DRCTSGetBookmarks.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getBookmarksSelf**
```objc
-(NSURLSessionTask*) getBookmarksSelfWithCompletionHandler: 
        (void (^)(DRCTSGetBookmarks* output, NSError* error)) handler;
```

Returns bookmarks of current user

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];



DRCTSBookmarksApi*apiInstance = [[DRCTSBookmarksApi alloc] init];

// Returns bookmarks of current user
[apiInstance getBookmarksSelfWithCompletionHandler: 
          ^(DRCTSGetBookmarks* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSBookmarksApi->getBookmarksSelf: %@", error);
                        }
                    }];
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**DRCTSGetBookmarks***](DRCTSGetBookmarks.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

