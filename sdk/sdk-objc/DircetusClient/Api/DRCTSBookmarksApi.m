#import "DRCTSBookmarksApi.h"
#import "DRCTSQueryParamCollection.h"
#import "DRCTSApiClient.h"
#import "DRCTSGetBookmark.h"
#import "DRCTSGetBookmarks.h"


@interface DRCTSBookmarksApi ()

@property (nonatomic, strong, readwrite) NSMutableDictionary *mutableDefaultHeaders;

@end

@implementation DRCTSBookmarksApi

NSString* kDRCTSBookmarksApiErrorDomain = @"DRCTSBookmarksApiErrorDomain";
NSInteger kDRCTSBookmarksApiMissingParamErrorCode = 234513;

@synthesize apiClient = _apiClient;

#pragma mark - Initialize methods

- (instancetype) init {
    return [self initWithApiClient:[DRCTSApiClient sharedClient]];
}


-(instancetype) initWithApiClient:(DRCTSApiClient *)apiClient {
    self = [super init];
    if (self) {
        _apiClient = apiClient;
        _mutableDefaultHeaders = [NSMutableDictionary dictionary];
    }
    return self;
}

#pragma mark -

-(NSString*) defaultHeaderForKey:(NSString*)key {
    return self.mutableDefaultHeaders[key];
}

-(void) setDefaultHeaderValue:(NSString*) value forKey:(NSString*)key {
    [self.mutableDefaultHeaders setValue:value forKey:key];
}

-(NSDictionary *)defaultHeaders {
    return self.mutableDefaultHeaders;
}

#pragma mark - Api Methods

///
/// Create a column in a given table
/// 
///  @param user [Directus user id] This assigns the bookmark to a specific user (there's a ticket to allow for \"global\" bookmarks using NULL) (Only using local connection) (optional)
///
///  @param title The text to display in the navigation menu (optional)
///
///  @param url The path to navigate to when clicked, relative to the Directus root (optional)
///
///  @param iconClass Deprecated (optional)
///
///  @param active Deprecated (optional)
///
///  @param section [\"search\" or \"other\"] Which nav section to show the link within. User generated bookmarks use \"search\", while all system links go within \"other\" (optional)
///
///  @returns void
///
-(NSURLSessionTask*) addBookmarkWithUser: (NSString*) user
    title: (NSString*) title
    url: (NSString*) url
    iconClass: (NSString*) iconClass
    active: (NSString*) active
    section: (NSString*) section
    completionHandler: (void (^)(NSError* error)) handler {
    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/bookmarks"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[@"application/x-www-form-urlencoded"]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];
    if (user) {
        formParams[@"user"] = user;
    }
    if (title) {
        formParams[@"title"] = title;
    }
    if (url) {
        formParams[@"url"] = url;
    }
    if (iconClass) {
        formParams[@"icon_class"] = iconClass;
    }
    if (active) {
        formParams[@"active"] = active;
    }
    if (section) {
        formParams[@"section"] = section;
    }

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"POST"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: nil
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler(error);
                                }
                            }];
}

///
/// Deletes specific bookmark
/// 
///  @param bookmarkId ID of table to return rows from 
///
///  @returns void
///
-(NSURLSessionTask*) deleteBookmarkWithBookmarkId: (NSNumber*) bookmarkId
    completionHandler: (void (^)(NSError* error)) handler {
    // verify the required parameter 'bookmarkId' is set
    if (bookmarkId == nil) {
        NSParameterAssert(bookmarkId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"bookmarkId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSBookmarksApiErrorDomain code:kDRCTSBookmarksApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/bookmarks/{bookmarkId}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (bookmarkId != nil) {
        pathParams[@"bookmarkId"] = bookmarkId;
    }

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"DELETE"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: nil
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler(error);
                                }
                            }];
}

///
/// Returns specific bookmark
/// 
///  @param bookmarkId ID of table to return rows from 
///
///  @returns DRCTSGetBookmark*
///
-(NSURLSessionTask*) getBookmarkWithBookmarkId: (NSNumber*) bookmarkId
    completionHandler: (void (^)(DRCTSGetBookmark* output, NSError* error)) handler {
    // verify the required parameter 'bookmarkId' is set
    if (bookmarkId == nil) {
        NSParameterAssert(bookmarkId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"bookmarkId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSBookmarksApiErrorDomain code:kDRCTSBookmarksApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/bookmarks/{bookmarkId}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (bookmarkId != nil) {
        pathParams[@"bookmarkId"] = bookmarkId;
    }

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"GET"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: @"DRCTSGetBookmark*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetBookmark*)data, error);
                                }
                            }];
}

///
/// Returns bookmarks
/// 
///  @returns DRCTSGetBookmarks*
///
-(NSURLSessionTask*) getBookmarksWithCompletionHandler: 
    (void (^)(DRCTSGetBookmarks* output, NSError* error)) handler {
    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/bookmarks"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"GET"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: @"DRCTSGetBookmarks*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetBookmarks*)data, error);
                                }
                            }];
}

///
/// Returns bookmarks of current user
/// 
///  @returns DRCTSGetBookmarks*
///
-(NSURLSessionTask*) getBookmarksSelfWithCompletionHandler: 
    (void (^)(DRCTSGetBookmarks* output, NSError* error)) handler {
    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/bookmarks/self"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"GET"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: @"DRCTSGetBookmarks*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetBookmarks*)data, error);
                                }
                            }];
}



@end
