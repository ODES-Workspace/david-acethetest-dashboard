# 🌀 Scoped Tailwind CSS in WordPress Plugin

This guide explains how to add **Tailwind CSS** to a WordPress plugin so that it **only applies to elements within a
container**, such as `<div id="my-plugin-content">`, and does **not conflict** with the existing WordPress theme.

---

## 📦 Prerequisites

Make sure your environment has Node.js and npm installed.

```bash
node -v
npm -v
```

## 📦 Install Vite

Run follwing in root directory of your plugin folder

```bash
npm create vite@latest . -- --template vue
```

## 📁 Install Modules

Run follwing in root directory of your plugin folder

```bash
npm i
```

## 📁 Configure vite.config.ts file

Put following data in vite (rename project folder)

```bash
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'node:path'

export default defineConfig({
  plugins: [vue()],
  root: path.resolve(__dirname),
  base: './acethetest-dashboard/dist/',
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'src/main.ts'),
    },
  },
})
```

## install type nodes

```bash
npm i @types/node
```

## ⚙️ Enqueue script in wordpress

```bash
add_action('wp_enqueue_scripts', 'my_vue_plugin_enqueue_assets');
function my_vue_plugin_enqueue_assets()
{
    $dir = plugin_dir_path(__FILE__);
    $uri = plugin_dir_url(__FILE__);


    // Dev mode
    if (defined('WP_ENV') && WP_ENV === 'development') {
        echo '<script type="module" src="http://localhost:5173/@vite/client"></script>';
        echo '<script type="module" src="http://localhost:5173/main.ts"></script>';
        return;
    }

    // Prod build
    $manifestPath = $dir . 'dist/.vite/manifest.json';
    if (!file_exists($manifestPath)) {
        return;
    }

    $manifest = json_decode(file_get_contents($manifestPath), true);
    $entry = $manifest['src/main.ts'];

    wp_enqueue_style('my-vue-plugin-style', $uri . 'dist/' . $entry['css'][0], [], null);
    wp_enqueue_script('my-vue-plugin-script', $uri . 'dist/' . $entry['file'], [], null, true);
}
```

## 🧩 Install tailwind

we use older version of tailwind otherwise it creates variable error

```bash
npm install -D tailwindcss@3 postcss autoprefixer
npx tailwindcss init -p
```

Then change tailwind.config.js to this:

```bash
/** @type {import('tailwindcss').Config} */
export default {
  content: ["./src/**/*.{html,ts,js,vue}"],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

Followed this link:
https://v3.tailwindcss.com/docs/installation

## Development Example

Add a constant in your plugin say: RUBICON_ROOMS_ENV and set it's value to "development" and change variable in enqueue
scrip to the same name
Then run following command and it'll work

```bash
npm run dev
```

## Production Example

Before going for production, change RUBICON_ROOMS_ENV to prod
Then run following command and it'll work

```bash
npm run build
```

## ✅ Usage Example

```bash
<div id="my-plugin-content">
  <div class="tw-bg-blue-500 tw-text-white tw-p-4">
    Tailwind-styled plugin block
  </div>
</div>
```

