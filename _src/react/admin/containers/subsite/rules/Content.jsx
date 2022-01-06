import React, { Component } from 'react';
import fetchWP from 'utils/fetchWP';
import {PaginationUI} from 'ui/admin/form';


export default class Table extends Component {

    constructor(props) {
		super(props);
		this.state = {
			pager: { current : 1, total : 0, pages : [] },
			items: [],
			columns : [],
			loading : true,
			error : false,
		};
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
		});

		this.getData = this.getData.bind(this);
	}

	getData = async ( page ) => {
		var url = this.props.url;
		this.fetchWP.get( url + '?page=' + page )
			.then( (json) => this.setState({
				items : json.items,
				pager : json.pager,
				columns : json.columns,
				loading : false,
				error : false,
			}), (err) => this.setState({ loading : false, error : true })
		);
	}

	async componentDidMount() {
		var page = this.props.match.params.page !== undefined ? this.props.match.params.page : 0;
		this.getData( page );
	}

    renderRows() {
		const { pager, items, columns } = this.state;
		return (
			<React.Fragment>
				<table className="uk-table uk-background-default">
					<thead>
						<tr>
							<th><input className="uk-checkbox hammock-top-checkbox" type="checkbox" /></th>
							{Object.keys(columns).map((column) =>
								<th key={column} className="uk-width-auto">{columns[column]}</th>
							)}
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><input className="uk-checkbox hammock-bottom-checkbox" type="checkbox" /></th>
							{Object.keys(columns).map((column) =>
								<th key={column} className="uk-width-auto">{columns[column]}</th>
							)}
						</tr>
					</tfoot>
					<tbody>
						{items.map(item =>
							<tr key={item.id}>
								<td><input className="uk-checkbox" type="checkbox" value={item.id} /></td>
								{Object.keys(columns).map((column) =>
									<td key={column} className={column}><span dangerouslySetInnerHTML={{ __html: item[column] }}></span></td>
								)}
							</tr>
						)}
					</tbody>
				</table>
				<PaginationUI pager={pager} onChange={this.getData}/>
			</React.Fragment>
		)
    }

    render() {
		const { pager, items } = this.state;
		var columns = this.props.columns;
        var hammock = this.props.hammock;
		return (
			<React.Fragment>
				<table className="uk-table uk-background-default">
					<thead>
						<tr>
							<th><input className="uk-checkbox hammock-top-checkbox" type="checkbox" /></th>
							{Object.keys(columns).map((column) =>
								<th key={column} className="uk-width-auto">{columns[column]}</th>
							)}
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><input className="uk-checkbox hammock-bottom-checkbox" type="checkbox" /></th>
							{Object.keys(columns).map((column) =>
								<th key={column} className="uk-width-auto">{columns[column]}</th>
							)}
						</tr>
					</tfoot>
					<tbody>
						{this.state.loading ? (
							<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1">
								<span className="uk-text-center" uk-spinner="ratio: 3"></span>
							</div>
						) : (
							items.map(item =>
								<tr key={item.id}>
									<td><input className="uk-checkbox" type="checkbox" value={item.id} /></td>
									{Object.keys(columns).map((column) =>
										<td key={column} className={column}><span dangerouslySetInnerHTML={{ __html: item[column] }}></span></td>
									)}
								</tr>
							)
						)}
					</tbody>
				</table>
				<PaginationUI pager={pager} onChange={this.getData}/>
			</React.Fragment>
		)
    }
}