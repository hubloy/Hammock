import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import fetchWP from 'utils/fetchWP';
import {PaginationUI} from 'ui/admin/form';

export default class SubSiteCodes extends Component {

	constructor(props) {
        super(props);
        
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
        
        this.state = {
            pager: {},
            items: [],
            loading : true,
            error : false,
        };
	}
	

	async componentDidMount() {
        this.loadPage();
    }

    async componentDidUpdate() {
        if ( typeof this.state.pager.current !== 'undefined' ) {
            this.loadPage();
        }
    }

    loadPage = async () => {
		const page = parseInt( location.hash.split("/").pop() ) || 1;
        const type = this.props.type;
        if ( page !== parseInt( this.state.pager.current ) ) {
            this.fetch_data( page, type );
        }
    }
    
    fetch_data = async ( page, type ) => {
        this.setState({ loading : true, error : false });
        this.fetchWP.get( 'codes/list/'+type+'?page=' + page )
            .then( (json) => this.setState({
                items : json.items,
                pager : json.pager,
                loading : false,
                error : false,
            }), (err) => this.setState({ loading : false, error : true, err : err })
        );
    }

	handleRowAction(event, id, action ) {
        event.preventDefault();
        console.log( id + ' ' + action );
        return false;
	}

	render() {
		const { pager, items } = this.state;
		const type = this.props.type,
			hubloy_membership = this.props.hubloy_membership,
			strings = hubloy_membership.strings;
		if ( this.state.loading) {
            return (
                <div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
                    <span className="uk-text-center" uk-spinner="ratio: 3"></span>
                </div>
            )
        } else {
            if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{hubloy_membership.error}</h3>
				)
			} else {
				return (
					<React.Fragment>
						{items.length <= 0 ? (
                            <h3 className="uk-text-center">{strings.not_found}</h3>
                        ) : (
                            <div>
                                <table className="uk-table uk-background-default">
                                    <thead>
                                        <tr>
                                            <th className='uk-table-shrink'><input className="uk-checkbox hubloy_membership-top-checkbox" type="checkbox" /></th>
                                            <th>{strings.table.code}</th>
                                            <th>{strings.table.status}</th>
                                            {type === 'coupons' && 
                                                <th>{strings.table.amount}</th>
                                            }
                                            <th>{strings.table.author}</th>
                                            <th>{strings.table.date}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th className='uk-table-shrink'><input className="uk-checkbox hubloy_membership-bottom-checkbox" type="checkbox" /></th>
                                            <th>{strings.table.code}</th>
                                            <th>{strings.table.status}</th>
                                            {type === 'coupons' && 
                                                <th>{strings.table.amount}</th>
                                            }
                                            <th>{strings.table.author}</th>
                                            <th>{strings.table.date}</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    {items.map(item =>
                                        <tr key={item.id} className={item.base_status}>
                                            <td className='uk-table-shrink'><input className="uk-checkbox" type="checkbox" value={item.id} /></td>
                                            <td>
                                                {item.code}
                                                <div id={"code-hover-"+ item.id}>
                                                    <Link uk-tooltip={hubloy_membership.common.buttons.edit} title={hubloy_membership.common.buttons.edit} className="uk-text-primary" to={"/edit/"+ item.id}>{hubloy_membership.common.buttons.edit}</Link>
                                                    {' '}|{' '}
                                                    <a href="#" data-id={item.id} uk-tooltip={hubloy_membership.common.buttons.delete} title={hubloy_membership.common.buttons.delete} className="uk-text-danger" onClick={ e => this.handleRowAction(e, item.id, 'delete')}>{hubloy_membership.common.buttons.delete}</a>
                                                </div>
                                            </td>
                                            <td>{item.status}</td>
                                            {type === 'coupons' && 
                                                <td><span dangerouslySetInnerHTML={{ __html: item.code_value }}></span></td>
                                            }
                                            <td>
                                                {item.author_data.name}
                                            </td>
                                            <td>{item.date_created}</td>
                                        </tr>
                                    )}
                                    </tbody>
                                </table>
                            </div>
						)}
						<PaginationUI pager={pager}/>
					</React.Fragment>
				)
			}
		}
	}
}