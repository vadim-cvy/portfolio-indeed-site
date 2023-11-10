import Control from "./Control"

export default class SearchTermControl extends Control
{
  public constructor()
  {
    super( 'searchTerm' )
  }

  public get val()
  {
    return String( super.val )
  }
}