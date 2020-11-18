# DirectusSDK::SettingsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**get_settings**](SettingsApi.md#get_settings) | **GET** /settings | Returns settings
[**get_settings_for**](SettingsApi.md#get_settings_for) | **GET** /settings/{collectionName} | Returns settings for collection
[**update_settings**](SettingsApi.md#update_settings) | **PUT** /settings/{collectionName} | Update settings


# **get_settings**
> GetSettings get_settings

Returns settings

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

api_instance = DirectusSDK::SettingsApi.new

begin
  #Returns settings
  result = api_instance.get_settings
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling SettingsApi->get_settings: #{e}"
end
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**GetSettings**](GetSettings.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_settings_for**
> GetSettingsFor get_settings_for(collection_name, )

Returns settings for collection

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

api_instance = DirectusSDK::SettingsApi.new

collection_name = 56 # Integer | Name of collection to return settings for


begin
  #Returns settings for collection
  result = api_instance.get_settings_for(collection_name, )
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling SettingsApi->get_settings_for: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **collection_name** | **Integer**| Name of collection to return settings for | 

### Return type

[**GetSettingsFor**](GetSettingsFor.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **update_settings**
> update_settings(collection_name, custom_data)

Update settings

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

api_instance = DirectusSDK::SettingsApi.new

collection_name = 56 # Integer | Name of collection to return settings for

custom_data = "custom_data_example" # String | Data based on your specific schema eg: active=1&title=LoremIpsum


begin
  #Update settings
  api_instance.update_settings(collection_name, custom_data)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling SettingsApi->update_settings: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **collection_name** | **Integer**| Name of collection to return settings for | 
 **custom_data** | **String**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



