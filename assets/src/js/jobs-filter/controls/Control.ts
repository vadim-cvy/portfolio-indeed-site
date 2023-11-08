export default class Control
{
  public constructor(
    public readonly name: string,
  ) {}

  protected _val: string | number = ''

  public get val()
  {
    return this._val
  }

  public change( val: typeof this._val )
  {
    this._val = val
  }
}