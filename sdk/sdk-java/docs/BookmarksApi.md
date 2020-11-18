# BookmarksApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**addBookmark**](BookmarksApi.md#addBookmark) | **POST** /bookmarks | Create a column in a given table
[**deleteBookmark**](BookmarksApi.md#deleteBookmark) | **DELETE** /bookmarks/{bookmarkId} | Deletes specific bookmark
[**getBookmark**](BookmarksApi.md#getBookmark) | **GET** /bookmarks/{bookmarkId} | Returns specific bookmark
[**getBookmarks**](BookmarksApi.md#getBookmarks) | **GET** /bookmarks | Returns bookmarks
[**getBookmarksSelf**](BookmarksApi.md#getBookmarksSelf) | **GET** /bookmarks/self | Returns bookmarks of current user


<a name="addBookmark"></a>
# **addBookmark**
> addBookmark(user, title, url, iconClass, active, section)

Create a column in a given table

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.BookmarksApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

BookmarksApi apiInstance = new BookmarksApi();
String user = "user_example"; // String | [Directus user id] This assigns the bookmark to a specific user (there's a ticket to allow for \"global\" bookmarks using NULL) (Only using local connection)
String title = "title_example"; // String | The text to display in the navigation menu
String url = "url_example"; // String | The path to navigate to when clicked, relative to the Directus root
String iconClass = "iconClass_example"; // String | Deprecated
String active = "active_example"; // String | Deprecated
String section = "section_example"; // String | [\"search\" or \"other\"] Which nav section to show the link within. User generated bookmarks use \"search\", while all system links go within \"other\"
try {
    apiInstance.addBookmark(user, title, url, iconClass, active, section);
} catch (ApiException e) {
    System.err.println("Exception when calling BookmarksApi#addBookmark");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **user** | **String**| [Directus user id] This assigns the bookmark to a specific user (there&#39;s a ticket to allow for \&quot;global\&quot; bookmarks using NULL) (Only using local connection) | [optional]
 **title** | **String**| The text to display in the navigation menu | [optional]
 **url** | **String**| The path to navigate to when clicked, relative to the Directus root | [optional]
 **iconClass** | **String**| Deprecated | [optional]
 **active** | **String**| Deprecated | [optional]
 **section** | **String**| [\&quot;search\&quot; or \&quot;other\&quot;] Which nav section to show the link within. User generated bookmarks use \&quot;search\&quot;, while all system links go within \&quot;other\&quot; | [optional]

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

<a name="deleteBookmark"></a>
# **deleteBookmark**
> deleteBookmark(bookmarkId)

Deletes specific bookmark

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.BookmarksApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

BookmarksApi apiInstance = new BookmarksApi();
Integer bookmarkId = 56; // Integer | ID of table to return rows from
try {
    apiInstance.deleteBookmark(bookmarkId);
} catch (ApiException e) {
    System.err.println("Exception when calling BookmarksApi#deleteBookmark");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **bookmarkId** | **Integer**| ID of table to return rows from |

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getBookmark"></a>
# **getBookmark**
> GetBookmark getBookmark(bookmarkId)

Returns specific bookmark

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.BookmarksApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

BookmarksApi apiInstance = new BookmarksApi();
Integer bookmarkId = 56; // Integer | ID of table to return rows from
try {
    GetBookmark result = apiInstance.getBookmark(bookmarkId);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling BookmarksApi#getBookmark");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **bookmarkId** | **Integer**| ID of table to return rows from |

### Return type

[**GetBookmark**](GetBookmark.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getBookmarks"></a>
# **getBookmarks**
> GetBookmarks getBookmarks()

Returns bookmarks

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.BookmarksApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

BookmarksApi apiInstance = new BookmarksApi();
try {
    GetBookmarks result = apiInstance.getBookmarks();
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling BookmarksApi#getBookmarks");
    e.printStackTrace();
}
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**GetBookmarks**](GetBookmarks.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getBookmarksSelf"></a>
# **getBookmarksSelf**
> GetBookmarks getBookmarksSelf()

Returns bookmarks of current user

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.BookmarksApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

BookmarksApi apiInstance = new BookmarksApi();
try {
    GetBookmarks result = apiInstance.getBookmarksSelf();
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling BookmarksApi#getBookmarksSelf");
    e.printStackTrace();
}
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**GetBookmarks**](GetBookmarks.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

