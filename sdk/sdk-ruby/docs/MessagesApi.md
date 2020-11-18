# DirectusSDK::MessagesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**get_message**](MessagesApi.md#get_message) | **GET** /messages/{messageId} | Returns specific message
[**get_messages**](MessagesApi.md#get_messages) | **GET** /messages/self | Returns messages


# **get_message**
> GetMessage get_message(message_id)

Returns specific message

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

api_instance = DirectusSDK::MessagesApi.new

message_id = 56 # Integer | ID of message to return


begin
  #Returns specific message
  result = api_instance.get_message(message_id)
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling MessagesApi->get_message: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **message_id** | **Integer**| ID of message to return | 

### Return type

[**GetMessage**](GetMessage.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_messages**
> GetMessages get_messages

Returns messages

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

api_instance = DirectusSDK::MessagesApi.new

begin
  #Returns messages
  result = api_instance.get_messages
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling MessagesApi->get_messages: #{e}"
end
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**GetMessages**](GetMessages.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



