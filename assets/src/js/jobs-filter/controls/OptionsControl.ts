import Control from "./Control"
import Option from "./Option"

export default class OptionsControl extends Control
{
  public constructor(
    name: string,
    public readonly options: Option[],
  )
  {
    super( name )
  }

  public change( val: typeof this._val )
  {
    super.change( val !== this._val ? val : '' )
  }
}