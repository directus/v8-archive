#import "DRCTSGetFilesData.h"

@implementation DRCTSGetFilesData

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
  return [[JSONKeyMapper alloc] initWithModelToJSONDictionary:@{ @"_id": @"id", @"active": @"active", @"name": @"name", @"url": @"url", @"title": @"title", @"location": @"location", @"caption": @"caption", @"type": @"type", @"charset": @"charset", @"tags": @"tags", @"width": @"width", @"height": @"height", @"size": @"size", @"embedId": @"embed_id", @"user": @"user", @"dateUploaded": @"date_uploaded", @"storageAdapter": @"storage_adapter" }];
}

/**
 * Indicates whether the property with the given name is optional.
 * If `propertyName` is optional, then return `YES`, otherwise return `NO`.
 * This method is used by `JSONModel`.
 */
+ (BOOL)propertyIsOptional:(NSString *)propertyName {

  NSArray *optionalProperties = @[@"_id", @"active", @"name", @"url", @"title", @"location", @"caption", @"type", @"charset", @"tags", @"width", @"height", @"size", @"embedId", @"user", @"dateUploaded", @"storageAdapter"];
  return [optionalProperties containsObject:propertyName];
}

@end
