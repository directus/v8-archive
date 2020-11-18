#import "DRCTSGetGroupsDataData.h"

@implementation DRCTSGetGroupsDataData

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
  return [[JSONKeyMapper alloc] initWithModelToJSONDictionary:@{ @"_id": @"id", @"name": @"name", @"_description": @"description", @"restrictToIpWhitelist": @"restrict_to_ip_whitelist", @"navOverride": @"nav_override", @"showActivity": @"show_activity", @"showMessages": @"show_messages", @"showUsers": @"show_users", @"showFiles": @"show_files" }];
}

/**
 * Indicates whether the property with the given name is optional.
 * If `propertyName` is optional, then return `YES`, otherwise return `NO`.
 * This method is used by `JSONModel`.
 */
+ (BOOL)propertyIsOptional:(NSString *)propertyName {

  NSArray *optionalProperties = @[@"_id", @"name", @"_description", @"restrictToIpWhitelist", @"navOverride", @"showActivity", @"showMessages", @"showUsers", @"showFiles"];
  return [optionalProperties containsObject:propertyName];
}

@end
