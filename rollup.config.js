import babel from 'rollup-plugin-babel';
import uglify from 'rollup-plugin-uglify';
import svelte from 'rollup-plugin-svelte';
import { sass } from 'svelte-preprocess-sass';
import commonjs from 'rollup-plugin-commonjs';
import resolve from 'rollup-plugin-node-resolve';

export default {
  input: 'src/main.js',
  output: {
    file: 'index.js',
    format: 'iife'
  },
  plugins: [
    resolve(),
    commonjs({
      include: 'node_modules/**'
    }),
    svelte({
      preprocess: {
        style: sass(),
      },
    }),
    babel(),
    uglify(),
  ]
};
