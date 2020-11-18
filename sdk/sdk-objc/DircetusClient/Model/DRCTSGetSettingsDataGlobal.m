#import "DRCTSGetSettingsDataGlobal.h"

@implementation DRCTSGetSettingsDataGlobal

- (instancetype)init {
  self = [super init];
  if (self) {
    // initialize property's default value, if any
    
  }
  return self;
}


/**
 * Maps json key to property name.
 * This method is used by `JSONModel`.
 */
+ (JSONKeyMapper *)keyMapper {
  return [[JSONKeyMapper alloc] initWithModelToJSONDictionary:@{ @"cmsUserAutoSignOut": @"cms_user_auto_sign_out", @"projectName": @"project_name", @"projectUrl": @"project_url", @"cmsColor": @"cms_color", @"rowsPerPage": @"rows_per_page", @"cmsThumbnailUrl": @"cms_thumbnail_url" }];
}

/**
 * Indicates whether the property with the given name is optional.
 * If `propertyName` is optional, then return `YES`, otherwise return `NO`.
 * This method is used by `JSONModel`.
 */
+ (BOOL)propertyIsOptional:(NSString *)propertyName {

  NSArray *optionalProperties = @[@"cmsUserAutoSignOut", @"projectName", @"projectUrl", @"cmsColor", @"rowsPerPage", @"cmsThumbnailUrl"];
  return [optionalProperties containsObject:propertyName];
}

@end
