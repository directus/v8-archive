# IO.Directus.Api.BookmarksApi

All URIs are relative to your configured base path, as per example.

Method | HTTP request | Description
------------- | ------------- | -------------
[**AddBookmark**](BookmarksApi.md#addbookmark) | **POST** /bookmarks | Create a column in a given table
[**DeleteBookmark**](BookmarksApi.md#deletebookmark) | **DELETE** /bookmarks/{bookmarkId} | Deletes specific bookmark
[**GetBookmark**](BookmarksApi.md#getbookmark) | **GET** /bookmarks/{bookmarkId} | Returns specific bookmark
[**GetBookmarks**](BookmarksApi.md#getbookmarks) | **GET** /bookmarks | Returns bookmarks
[**GetBookmarksSelf**](BookmarksApi.md#getbookmarksself) | **GET** /bookmarks/self | Returns bookmarks of current user


<a name="addbookmark"></a>
# **AddBookmark**
> void AddBookmark (string user = null, string title = null, string url = null, string iconClass = null, string active = null, string section = null)

Create a column in a given table

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class AddBookmarkExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new BookmarksApi();
            var user = user_example;  // string | [Directus user id] This assigns the bookmark to a specific user (there's a ticket to allow for \"global\" bookmarks using NULL) (Only using local connection) (optional) 
            var title = title_example;  // string | The text to display in the navigation menu (optional) 
            var url = url_example;  // string | The path to navigate to when clicked, relative to the Directus root (optional) 
            var iconClass = iconClass_example;  // string | Deprecated (optional) 
            var active = active_example;  // string | Deprecated (optional) 
            var section = section_example;  // string | [\"search\" or \"other\"] Which nav section to show the link within. User generated bookmarks use \"search\", while all system links go within \"other\" (optional) 

            try
            {
                // Create a column in a given table
                apiInstance.AddBookmark(user, title, url, iconClass, active, section);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling BookmarksApi.AddBookmark: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **user** | **string**| [Directus user id] This assigns the bookmark to a specific user (there&#39;s a ticket to allow for \&quot;global\&quot; bookmarks using NULL) (Only using local connection) | [optional] 
 **title** | **string**| The text to display in the navigation menu | [optional] 
 **url** | **string**| The path to navigate to when clicked, relative to the Directus root | [optional] 
 **iconClass** | **string**| Deprecated | [optional] 
 **active** | **string**| Deprecated | [optional] 
 **section** | **string**| [\&quot;search\&quot; or \&quot;other\&quot;] Which nav section to show the link within. User generated bookmarks use \&quot;search\&quot;, while all system links go within \&quot;other\&quot; | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="deletebookmark"></a>
# **DeleteBookmark**
> void DeleteBookmark (int? bookmarkId)

Deletes specific bookmark

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class DeleteBookmarkExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new BookmarksApi();
            var bookmarkId = 56;  // int? | ID of table to return rows from

            try
            {
                // Deletes specific bookmark
                apiInstance.DeleteBookmark(bookmarkId);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling BookmarksApi.DeleteBookmark: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **bookmarkId** | **int?**| ID of table to return rows from | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="getbookmark"></a>
# **GetBookmark**
> GetBookmark GetBookmark (int? bookmarkId)

Returns specific bookmark

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetBookmarkExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new BookmarksApi();
            var bookmarkId = 56;  // int? | ID of table to return rows from

            try
            {
                // Returns specific bookmark
                GetBookmark result = apiInstance.GetBookmark(bookmarkId);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling BookmarksApi.GetBookmark: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **bookmarkId** | **int?**| ID of table to return rows from | 

### Return type

[**GetBookmark**](GetBookmark.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="getbookmarks"></a>
# **GetBookmarks**
> GetBookmarks GetBookmarks ()

Returns bookmarks

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetBookmarksExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new BookmarksApi();

            try
            {
                // Returns bookmarks
                GetBookmarks result = apiInstance.GetBookmarks();
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling BookmarksApi.GetBookmarks: " + e.Message );
            }
        }
    }
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

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="getbookmarksself"></a>
# **GetBookmarksSelf**
> GetBookmarks GetBookmarksSelf ()

Returns bookmarks of current user

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetBookmarksSelfExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new BookmarksApi();

            try
            {
                // Returns bookmarks of current user
                GetBookmarks result = apiInstance.GetBookmarksSelf();
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling BookmarksApi.GetBookmarksSelf: " + e.Message );
            }
        }
    }
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

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

