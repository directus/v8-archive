import Colors from 'colors'; // eslint-disable-line

// Added so the 'Colors' import doesn't get removed for not being referenced.
Colors.black;

export const log = {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  info: (msg: string, ...args: any[]): void => console.log('info'.cyan, 'directus'.blue, msg, ...args),
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  warn: (msg: string, ...args: any[]): void => console.log('warning'.yellow, 'directus'.blue, msg, ...args),
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  error: (msg: string, ...args: any[]): void => console.error('error'.red, 'directus'.blue, msg, ...args),
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  success: (msg: string, ...args: any[]): void => console.log('success'.green, 'directus'.blue, msg, ...args),
};
