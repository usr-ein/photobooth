import js from "@eslint/js"
import eslintConfigPrettier from "eslint-config-prettier/flat"
import eslintPluginReact from "eslint-plugin-react"
import reactHooks from "eslint-plugin-react-hooks"
import reactRefresh from "eslint-plugin-react-refresh"
import globals from "globals"
import tseslint from "typescript-eslint"

export default tseslint.config(
    {ignores: ["dist"]},
    {
        extends: [
            js.configs.recommended,
            ...tseslint.configs.recommended,
            eslintConfigPrettier,
        ],
        files: ["**/*.{ts,tsx}"],
        languageOptions: {
            ecmaVersion: 2020,
            globals: globals.browser,
        },
        plugins: {
            "react-hooks": reactHooks,
            "react-refresh": reactRefresh,
            react: eslintPluginReact,
        },
        rules: {
            ...reactHooks.configs.recommended.rules,
            "react-refresh/only-export-components": [
                "warn",
                {allowConstantExport: true},
            ],
            "react/prop-types": "off",
            "@typescript-eslint/no-non-null-assertion": "error",
            "linebreak-style": ["error", "unix"],
            "max-lines": [
                "error",
                {
                    max: 400,
                    skipBlankLines: true,
                    skipComments: true,
                },
            ],
        },
    },
)
