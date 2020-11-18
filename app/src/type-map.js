/**
 * The Grand Directus Types To Whatever Database Type Mapping™
 */

export const datatypes = {
	mysql: {
		// String based
		// -------------------------------------------------------------------------
		CHAR: {
			length: true,
			defaultLength: 50,
			maxLength: 255,
			description: 'datatypes.mysql.CHAR',
			fallbackInterface: 'text-input'
		},

		VARCHAR: {
			length: true,
			defaultLength: 255,
			maxLength: 65535,
			description: 'datatypes.mysql.VARCHAR',
			fallbackInterface: 'text-input'
		},

		TINYTEXT: {
			description: 'datatypes.mysql.TINYTEXT',
			fallbackInterface: 'textarea'
		},

		TEXT: {
			description: 'datatypes.mysql.TEXT',
			fallbackInterface: 'textarea'
		},

		MEDIUMTEXT: {
			description: 'datatypes.mysql.MEDIUMTEXT',
			fallbackInterface: 'textarea'
		},

		LONGTEXT: {
			description: 'datatypes.mysql.LONGTEXT',
			fallbackInterface: 'textarea'
		},

		// Numeric
		// -------------------------------------------------------------------------
		TINYINT: {
			description: 'datatypes.mysql.TINYINT',
			fallbackInterface: 'switch'
		},

		SMALLINT: {
			description: 'datatypes.mysql.SMALLINT',
			fallbackInterface: 'numeric'
		},

		MEDIUMINT: {
			description: 'datatypes.mysql.MEDIUMINT',
			fallbackInterface: 'numeric'
		},

		INT: {
			description: 'datatypes.mysql.INT',
			fallbackInterface: 'numeric'
		},

		BIGINT: {
			description: 'datatypes.mysql.BIGINT',
			fallbackInterface: 'numeric'
		},

		// Decimal Numbers
		// -------------------------------------------------------------------------
		DECIMAL: {
			decimal: true,
			description: 'datatypes.mysql.DECIMAL',
			defaultDigits: 10,
			maxDigits: 65,
			defaultDecimals: 10,
			maxDecimals: 30,
			fallbackInterface: 'numeric'
		},

		FLOAT: {
			decimal: true,
			description: 'datatypes.mysql.FLOAT',
			defaultDigits: 10,
			defaultDecimals: 10,
			fallbackInterface: 'numeric'
		},

		DOUBLE: {
			decimal: true,
			description: 'datatypes.mysql.DOUBLE',
			defaultDigits: 10,
			defaultDecimals: 10,
			fallbackInterface: 'numeric'
		},

		// Date and Time
		// -------------------------------------------------------------------------
		DATE: {
			description: 'datatypes.mysql.DATE',
			fallbackInterface: 'date'
		},

		DATETIME: {
			description: 'datatypes.mysql.DATETIME',
			fallbackInterface: 'datetime'
		},

		TIME: {
			description: 'datatypes.mysql.TIME',
			fallbackInterface: 'time'
		},

		TIMESTAMP: {
			description: 'datatypes.mysql.TIMESTAMP',
			fallbackInterface: 'time'
		},

		YEAR: {
			description: 'datatypes.mysql.YEAR',
			fallbackInterface: 'numeric'
		}
	}
};

export default {
	alias: {
		description: 'fieldtypes.alias',
		mysql: {
			datatypes: null,
			default: null
		}
	},
	array: {
		description: 'fieldtypes.array',
		mysql: {
			datatypes: ['VARCHAR'],
			default: 'VARCHAR'
		}
	},
	boolean: {
		description: 'fieldtypes.boolean',
		mysql: {
			datatypes: ['TINYINT'],
			default: 'TINYINT'
		}
	},
	date: {
		description: 'fieldtypes.date',
		mysql: {
			datatypes: ['DATE'],
			default: 'DATE'
		}
	},
	datetime: {
		description: 'fieldtypes.datetime',
		mysql: {
			datatypes: ['DATETIME'],
			default: 'DATETIME'
		}
	},
	datetime_created: {
		description: 'fieldtypes.datetime_created',
		mysql: {
			datatypes: ['DATETIME'],
			default: 'DATETIME'
		}
	},
	datetime_updated: {
		description: 'fieldtypes.datetime_updated',
		mysql: {
			datatypes: ['DATETIME'],
			default: 'DATETIME'
		}
	},
	decimal: {
		description: 'fieldtypes.decimal',
		mysql: {
			datatypes: ['DECIMAL', 'FLOAT', 'DOUBLE'],
			default: 'DECIMAL'
		}
	},
	time: {
		description: 'fieldtypes.time',
		mysql: {
			datatypes: ['TIME'],
			default: 'TIME'
		}
	},
	file: {
		description: 'fieldtypes.file',
		mysql: {
			datatypes: ['INT'],
			default: 'INT'
		}
	},
	group: {
		description: 'fieldtypes.group',
		mysql: {
			datatypes: null,
			default: null
		}
	},
	integer: {
		description: 'fieldtypes.integer',
		mysql: {
			datatypes: ['TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT'],
			default: 'INT'
		}
	},
	json: {
		description: 'fieldtypes.json',
		mysql: {
			datatypes: ['VARCHAR', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT'],
			default: 'TEXT'
		}
	},
	lang: {
		description: 'fieldtypes.lang',
		mysql: {
			datatypes: ['CHAR', 'VARCHAR'],
			default: 'CHAR'
		}
	},
	m2o: {
		description: 'fieldtypes.m2o',
		mysql: {
			datatypes: ['CHAR', 'VARCHAR', 'INT', 'BIGINT'],
			default: 'INT'
		}
	},
	o2m: {
		description: 'fieldtypes.o2m',
		mysql: {
			datatypes: null,
			default: null
		}
	},
	sort: {
		description: 'fieldtypes.sort',
		mysql: {
			datatypes: ['TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT'],
			default: 'INT'
		}
	},
	status: {
		description: 'fieldtypes.status',
		mysql: {
			datatypes: ['CHAR', 'VARCHAR', 'INT'],
			default: 'VARCHAR'
		}
	},
	string: {
		description: 'fieldtypes.string',
		mysql: {
			datatypes: ['CHAR', 'VARCHAR', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT'],
			default: 'VARCHAR'
		}
	},
	slug: {
		description: 'fieldtypes.slug',
		mysql: {
			datatypes: ['CHAR', 'VARCHAR', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT'],
			default: 'VARCHAR'
		}
	},
	translation: {
		description: 'fieldtypes.translation',
		mysql: {
			datatypes: null,
			default: null
		}
	},
	uuid: {
		description: 'fieldtypes.uuid',
		mysql: {
			datatypes: ['VARCHAR'],
			default: 'VARCHAR'
		}
	},
	user: {
		description: 'fieldtypes.user',
		mysql: {
			datatypes: ['INT'],
			default: 'INT'
		}
	},
	owner: {
		description: 'fieldtypes.owner',
		mysql: {
			datatypes: ['INT'],
			default: 'INT'
		}
	},
	user_updated: {
		description: 'fieldtypes.user_updated',
		mysql: {
			datatypes: ['INT'],
			default: 'INT'
		}
	},
	hash: {
		description: 'fieldtypes.hash',
		mysql: {
			datatypes: ['VARCHAR'],
			default: 'VARCHAR'
		}
	}
};
