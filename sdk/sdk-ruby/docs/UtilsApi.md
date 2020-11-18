# DirectusSDK::UtilsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**get_hash**](UtilsApi.md#get_hash) | **POST** /hash | Get a hashed value
[**get_random**](UtilsApi.md#get_random) | **POST** /random | Returns random alphanumeric string


# **get_hash**
> get_hash(opts)

Get a hashed value

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

api_instance = DirectusSDK::UtilsApi.new

opts = { 
  string: "string_example", # String | The string to be hashed
  hasher: "core", # String | The hasher used to hash the given string
  options: "options_example" # String | The hasher options
}

begin
  #Get a hashed value
  api_instance.get_hash(opts)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling UtilsApi->get_hash: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **string** | **String**| The string to be hashed | [optional] 
 **hasher** | **String**| The hasher used to hash the given string | [optional] [default to core]
 **options** | **String**| The hasher options | [optional] 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



# **get_random**
> get_random(opts)

Returns random alphanumeric string

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

api_instance = DirectusSDK::UtilsApi.new

opts = { 
  length: "length_example" # String | Integer(String) for length of random string
}

begin
  #Returns random alphanumeric string
  api_instance.get_random(opts)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling UtilsApi->get_random: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **length** | **String**| Integer(String) for length of random string | [optional] 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



