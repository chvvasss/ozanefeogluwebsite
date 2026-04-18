import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import Link from "@tiptap/extension-link";

/**
 * Alpine component that wires a TipTap editor into a Blade form. The component
 * renders into the `x-ref="editor"` element and mirrors the HTML output into a
 * hidden `x-ref="hidden"` textarea so the value is submitted with the form.
 */
export function tiptapEditor(initialHtml = "") {
  let editor = null;

  return {
    _editor() {
      return editor;
    },

    init() {
      editor = new Editor({
        element: this.$refs.editor,
        content: initialHtml || "<p></p>",
        editorProps: {
          attributes: {
            class: "tiptap-prose",
          },
        },
        extensions: [
          StarterKit.configure({
            heading: { levels: [2, 3] },
            codeBlock: {},
          }),
          Link.configure({
            openOnClick: false,
            autolink: true,
            HTMLAttributes: {
              rel: "noopener noreferrer",
              target: "_blank",
            },
          }),
        ],
        onUpdate: () => {
          this.$refs.hidden.value = editor.getHTML();
        },
        onCreate: () => {
          this.$refs.hidden.value = editor.getHTML();
        },
      });
    },

    destroy() {
      editor?.destroy();
      editor = null;
    },

    chain(method, ...args) {
      if (!editor) return;
      const ch = editor.chain().focus();
      if (typeof ch[method] === "function") {
        ch[method](...args).run();
      }
    },

    active(name, attrs) {
      return editor?.isActive(name, attrs) ?? false;
    },

    setLink() {
      if (!editor) return;
      const prev = editor.getAttributes("link").href ?? "";
      const url = window.prompt("URL", prev);
      if (url === null) return;
      if (url === "") {
        editor.chain().focus().extendMarkRange("link").unsetLink().run();
        return;
      }
      editor.chain().focus().extendMarkRange("link").setLink({ href: url }).run();
    },

    unsetLink() {
      editor?.chain().focus().unsetLink().run();
    },
  };
}
