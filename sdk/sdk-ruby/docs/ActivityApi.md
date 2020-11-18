# DirectusSDK::ActivityApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**get_activity**](ActivityApi.md#get_activity) | **GET** /activity | Returns activity


# **get_activity**
> GetActivity get_activity

Returns activity

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

api_instance = DirectusSDK::ActivityApi.new

begin
  #Returns activity
  result = api_instance.get_activity
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling ActivityApi->get_activity: #{e}"
end
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**GetActivity**](GetActivity.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



