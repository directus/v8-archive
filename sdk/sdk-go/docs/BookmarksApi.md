# \BookmarksApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**AddBookmark**](BookmarksApi.md#AddBookmark) | **Post** /bookmarks | Create a column in a given table
[**DeleteBookmark**](BookmarksApi.md#DeleteBookmark) | **Delete** /bookmarks/{bookmarkId} | Deletes specific bookmark
[**GetBookmark**](BookmarksApi.md#GetBookmark) | **Get** /bookmarks/{bookmarkId} | Returns specific bookmark
[**GetBookmarks**](BookmarksApi.md#GetBookmarks) | **Get** /bookmarks | Returns bookmarks
[**GetBookmarksSelf**](BookmarksApi.md#GetBookmarksSelf) | **Get** /bookmarks/self | Returns bookmarks of current user


# **AddBookmark**
> AddBookmark(ctx, optional)
Create a column in a given table

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
 **optional** | **map[string]interface{}** | optional parameters | nil if no parameters

### Optional Parameters
Optional parameters are passed through a map[string]interface{}.

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **user** | **string**| [Directus user id] This assigns the bookmark to a specific user (there&#39;s a ticket to allow for \&quot;global\&quot; bookmarks using NULL) (Only using local connection) | 
 **title** | **string**| The text to display in the navigation menu | 
 **url** | **string**| The path to navigate to when clicked, relative to the Directus root | 
 **iconClass** | **string**| Deprecated | 
 **active** | **string**| Deprecated | 
 **section** | **string**| [\&quot;search\&quot; or \&quot;other\&quot;] Which nav section to show the link within. User generated bookmarks use \&quot;search\&quot;, while all system links go within \&quot;other\&quot; | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **DeleteBookmark**
> DeleteBookmark(ctx, bookmarkId)
Deletes specific bookmark

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **bookmarkId** | **int32**| ID of table to return rows from | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetBookmark**
> GetBookmark GetBookmark(ctx, bookmarkId)
Returns specific bookmark

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **bookmarkId** | **int32**| ID of table to return rows from | 

### Return type

[**GetBookmark**](GetBookmark.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetBookmarks**
> GetBookmarks GetBookmarks(ctx, )
Returns bookmarks

### Required Parameters
This endpoint does not need any parameter.

### Return type

[**GetBookmarks**](GetBookmarks.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetBookmarksSelf**
> GetBookmarks GetBookmarksSelf(ctx, )
Returns bookmarks of current user

### Required Parameters
This endpoint does not need any parameter.

### Return type

[**GetBookmarks**](GetBookmarks.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

