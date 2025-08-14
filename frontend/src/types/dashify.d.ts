declare module 'dashify' {
  export default function dashify(
    input: string,
    options?: { condense?: boolean; lowercase?: boolean }
  ): string
}
