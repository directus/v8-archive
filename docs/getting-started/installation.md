# ⚙️ Installation

Directus runs on PHP and MySQL, meaning it can be installed in numerous different ways and environments. 

### Recommended

<div>
	<InstallLink link="/installation/git">
		<template #icon>
			<svg viewBox="0 0 92 92" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2"><path d="M90.154 41.965L50.035 1.848a5.918 5.918 0 00-8.369 0l-8.331 8.331 10.568 10.568a7.022 7.022 0 017.229 1.685 7.03 7.03 0 011.67 7.275l10.186 10.184a7.03 7.03 0 017.275 1.671 7.043 7.043 0 11-11.492 2.299l-9.5-9.499v24.997a7.042 7.042 0 11-8.096 11.291 7.042 7.042 0 012.307-11.496V33.926a7.037 7.037 0 01-3.822-9.234l-10.418-10.42-27.51 27.507a5.922 5.922 0 000 8.371l40.121 40.118a5.92 5.92 0 008.369 0l39.932-39.931a5.921 5.921 0 000-8.372z" fill="#fff" fill-rule="nonzero"/></svg>
		</template>
		<template #title>Git</template>
		<template #description>
			Pulling the codebase from git ensures that you can easily upgrade to newer versions in the future.
		</template>
	</InstallLink>
</div>

<div>
	<InstallLink link="/installation/docker">
		<template #icon>
			<svg xmlns="http://www.w3.org/2000/svg" width="34" height="23"><g fill="#fff" fill-rule="evenodd"><path d="M18.8017 10.5442h3.4333v-3.101h-3.4333zM14.745 10.5442h3.4333v-3.101H14.745v3.101zM10.6892 10.5442h3.4325v-3.101h-3.4334v3.101zM6.6316 10.5442h3.4334v-3.101H6.6316zM2.5759 10.5442h3.4324v-3.101H2.576v3.101zM6.6326 6.8226h3.4324v-3.101H6.6316v3.101zM10.6892 6.8226h3.4325v-3.101h-3.4334v3.101zM14.745 6.8226h3.4333v-3.101H14.745v3.101zM14.745 3.101h3.4333V0H14.745v3.101z"></path><path d="M28.752 8.3043c-.1708-1.2412-.8667-2.317-2.1326-3.2901l-.727-.482-.4866.7243c-.6197.9309-.9318 2.2216-.829 3.46.046.4351.19 1.2145.6408 1.8993-.4498.2405-1.3366.572-2.5144.549H.1285l-.045.2589c-.2111 1.2439-.2075 5.1252 2.329 8.1087 1.9269 2.2675 4.8168 3.4178 8.5889 3.4178 8.1757 0 14.2245-3.741 17.0565-10.5406 1.1136.022 3.5132.0064 4.7461-2.3326.0312-.0533.1056-.1947.3204-.638l.1184-.2424-.693-.46c-.75-.4984-2.4723-.681-3.7979-.4323z"></path></g></svg>
		</template>
		<template #title>Docker</template>
		<template #description>
			Has everything you need to get started; no need to install, configure, or manage additional packages on your server.
		</template>
	</InstallLink>
</div>

<div>
	<InstallLink link="/installation/do-one-click">
		<template #icon>
			<svg width="178" height="177" viewBox="0 0 178 177" xmlns="http://www.w3.org/2000/svg"><g fill="#fff" fill-rule="evenodd"><path d="M89 176.5v-34.2c36.2 0 64.3-35.9 50.4-74-5.1-14-16.4-25.3-30.5-30.4-38.1-13.8-74 14.2-74 50.4H.8C.8 30.6 56.6-14.4 117.1 4.5c26.4 8.3 47.5 29.3 55.7 55.7 18.9 60.5-26.1 116.3-83.8 116.3z" fill-rule="nonzero"/><path d="M89.1 142.5H55v-34.1h34.1zM55 168.6H28.9v-26.1H55zM28.9 142.5H7v-21.9h21.9v21.9z"/></g></svg>
		</template>
		<template #title>DigitalOcean One-Click</template>
		<template #description>
			Will spin up a new server and install everything you need automagically.
		</template>
	</InstallLink>
</div>

<div>
	<InstallLink link="/installation/cloud">
		<template #icon>
			<svg viewBox="0 0 66 40" width="66" height="40" xmlns="http://www.w3.org/2000/svg">
				<path d="M52.6937 25.2423c-.3038-.076-.557-.152-.785-.2532-.2278-.1013-.405-.2279-.557-.3798.152-1.342 0-2.5068.1266-3.8235.5064-5.115 3.7223-3.4944 6.6089-4.33 1.7978-.5064 3.5956-1.5446 4.0514-3.6716-1.975-2.3042-4.178-4.33-6.5835-6.0518C47.7308 1.1617 37.5009-1.0666 28.208.478c1.4568 2.544 3.6278 4.4952 6.1463 5.6973 0 0-2.5185 0-4.677-1.6124-.633.2533-1.8998.7515-2.5075 1.0554 4.9377 4.735 12.6607 5.2668 18.206 1.0128-.0252.0507-.5064.785-1.0887 3.8489-1.2914 6.5329-5.0137 6.0264-9.6222 4.3806-9.5714-3.469-14.8383-.2533-19.624-6.8368-1.3927.785-2.2536 2.2536-2.2536 3.8488 0 1.646.9116 3.0386 2.2283 3.7982.7188-.9535 1.041-1.2248 2.2947-1.2248-1.94 1.0998-2.1681 2.0604-3.0037 4.7192-1.0129 3.2158-.5824 6.5076-5.3175 7.3685-2.5068.1266-2.4562 1.8231-3.3678 4.3553C4.482 34.1808 2.9626 35.4975 0 38.612c1.2154 1.4686 2.4815 1.6459 3.7729 1.1141 2.6587-1.1141 4.7098-4.5578 6.6342-6.7861 2.1523-2.4815 7.3179-1.418 11.2173-3.8488 2.6841-1.646 4.0008-3.8742 2.2283-7.647 1.1395 1.266 1.8232 2.8612 1.9244 4.5578 4.5072-.5824 10.5337 4.9123 16.0285 5.8239-.5571-.709-1.0129-1.4687-1.342-2.2536-.6331-1.5193-.8357-2.912-.709-4.1274.5064 3.0132 3.545 6.8874 8.432 7.2419 1.2407.1013 2.608-.0507 4.026-.4811 1.6966-.5064 3.2665-1.1648 5.1403-.8103 1.3926.2532 2.684.9622 3.4943 2.1523 1.2154 1.7725 3.8742 2.1523 5.0643-.0253-2.684-7.014-10.078-7.4698-13.2178-8.28z" fill="#FFF" fill-rule="evenodd"></path>
			</svg>
		</template>
		<template #title>Directus Cloud</template>
		<template #description>
			Don't bother installing Directus yourself. Let us do it!
		</template>
	</InstallLink>
</div>


### Other

#### [Manual file upload (FTP)](/installation/manual)

#### [Building from source](/installation/from-source)