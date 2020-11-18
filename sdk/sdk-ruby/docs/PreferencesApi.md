# DirectusSDK::PreferencesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**get_preferences**](PreferencesApi.md#get_preferences) | **GET** /tables/{tableId}/preferences | Returns table preferences
[**update_preferences**](PreferencesApi.md#update_preferences) | **PUT** /tables/{tableId}/preferences | Update table preferences


# **get_preferences**
> GetPreferences get_preferences(table_id)

Returns table preferences

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

api_instance = DirectusSDK::PreferencesApi.new

table_id = "table_id_example" # String | ID of table to return rows from


begin
  #Returns table preferences
  result = api_instance.get_preferences(table_id)
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling PreferencesApi->get_preferences: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 

### Return type

[**GetPreferences**](GetPreferences.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **update_preferences**
> update_preferences(table_id, opts)

Update table preferences

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

api_instance = DirectusSDK::PreferencesApi.new

table_id = "table_id_example" # String | ID of table to return rows from

opts = { 
  id: "id_example", # String | Preference's Unique Identification number
  table_name: "table_name_example", # String | Name of table to add
  columns_visible: "columns_visible_example", # String | List of visible columns, separated by commas
  sort: 56, # Integer | The sort order of the column used to override the column order in the schema
  sort_order: "sort_order_example", # String | Sort Order (ASC=Ascending or DESC=Descending)
  status: "status_example" # String | List of status values. separated by comma
}

begin
  #Update table preferences
  api_instance.update_preferences(table_id, opts)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling PreferencesApi->update_preferences: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 
 **id** | **String**| Preference&#39;s Unique Identification number | [optional] 
 **table_name** | **String**| Name of table to add | [optional] 
 **columns_visible** | **String**| List of visible columns, separated by commas | [optional] 
 **sort** | **Integer**| The sort order of the column used to override the column order in the schema | [optional] 
 **sort_order** | **String**| Sort Order (ASC&#x3D;Ascending or DESC&#x3D;Descending) | [optional] 
 **status** | **String**| List of status values. separated by comma | [optional] 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



