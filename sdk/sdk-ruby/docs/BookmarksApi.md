# DirectusSDK::BookmarksApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**add_bookmark**](BookmarksApi.md#add_bookmark) | **POST** /bookmarks | Create a column in a given table
[**delete_bookmark**](BookmarksApi.md#delete_bookmark) | **DELETE** /bookmarks/{bookmarkId} | Deletes specific bookmark
[**get_bookmark**](BookmarksApi.md#get_bookmark) | **GET** /bookmarks/{bookmarkId} | Returns specific bookmark
[**get_bookmarks**](BookmarksApi.md#get_bookmarks) | **GET** /bookmarks | Returns bookmarks
[**get_bookmarks_self**](BookmarksApi.md#get_bookmarks_self) | **GET** /bookmarks/self | Returns bookmarks of current user


# **add_bookmark**
> add_bookmark(opts)

Create a column in a given table

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::BookmarksApi.new

opts = { 
  user: "user_example", # String | [Directus user id] This assigns the bookmark to a specific user (there's a ticket to allow for \"global\" bookmarks using NULL) (Only using local connection)
  title: "title_example", # String | The text to display in the navigation menu
  url: "url_example", # String | The path to navigate to when clicked, relative to the Directus root
  icon_class: "icon_class_example", # String | Deprecated
  active: "active_example", # String | Deprecated
  section: "section_example" # String | [\"search\" or \"other\"] Which nav section to show the link within. User generated bookmarks use \"search\", while all system links go within \"other\"
}

begin
  #Create a column in a given table
  api_instance.add_bookmark(opts)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling BookmarksApi->add_bookmark: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **user** | **String**| [Directus user id] This assigns the bookmark to a specific user (there&#39;s a ticket to allow for \&quot;global\&quot; bookmarks using NULL) (Only using local connection) | [optional] 
 **title** | **String**| The text to display in the navigation menu | [optional] 
 **url** | **String**| The path to navigate to when clicked, relative to the Directus root | [optional] 
 **icon_class** | **String**| Deprecated | [optional] 
 **active** | **String**| Deprecated | [optional] 
 **section** | **String**| [\&quot;search\&quot; or \&quot;other\&quot;] Which nav section to show the link within. User generated bookmarks use \&quot;search\&quot;, while all system links go within \&quot;other\&quot; | [optional] 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



# **delete_bookmark**
> delete_bookmark(bookmark_id)

Deletes specific bookmark

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::BookmarksApi.new

bookmark_id = 56 # Integer | ID of table to return rows from


begin
  #Deletes specific bookmark
  api_instance.delete_bookmark(bookmark_id)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling BookmarksApi->delete_bookmark: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **bookmark_id** | **Integer**| ID of table to return rows from | 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_bookmark**
> GetBookmark get_bookmark(bookmark_id)

Returns specific bookmark

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::BookmarksApi.new

bookmark_id = 56 # Integer | ID of table to return rows from


begin
  #Returns specific bookmark
  result = api_instance.get_bookmark(bookmark_id)
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling BookmarksApi->get_bookmark: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **bookmark_id** | **Integer**| ID of table to return rows from | 

### Return type

[**GetBookmark**](GetBookmark.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_bookmarks**
> GetBookmarks get_bookmarks

Returns bookmarks

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::BookmarksApi.new

begin
  #Returns bookmarks
  result = api_instance.get_bookmarks
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling BookmarksApi->get_bookmarks: #{e}"
end
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



# **get_bookmarks_self**
> GetBookmarks get_bookmarks_self

Returns bookmarks of current user

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::BookmarksApi.new

begin
  #Returns bookmarks of current user
  result = api_instance.get_bookmarks_self
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling BookmarksApi->get_bookmarks_self: #{e}"
end
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



