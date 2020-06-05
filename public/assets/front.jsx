'use strict';

class DataItem extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            checked: props.name,
            names: ['h1', 'p']
        };

        this.change = this.change.bind(this);
        this.changeText = this.changeText.bind(this);
    }

    render() {
        return (
            <li>
                <select value={this.state.checked} onChange={this.change} >
                    {
                        this.state.names.map((name, k) => {
                            return (
                                <option key={k} value={name}>{name}</option>
                            );
                        })
                    }
                </select>
                <input type='text' value={this.state.value} onChange={this.changeText} />
            </li>
        );
    }

    change(e) {
        let newval = e.target.value;
        if (this.props.onChange) {
            this.props.onChange(this.props.number, newval)
        }
        this.setState({checked: newval});
    }

    changeText(e) {
        let newval = e.target.value;
        if (this.props.onChangeText) {
            this.props.onChangeText(this.props.number, newval)
        }
    }
}

class DataList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            message: null,
            items: []
        };

        this.add = this.add.bind(this);
        this.save = this.save.bind(this);
        this.updateItem = this.updateItem.bind(this);
        this.updateItemText = this.updateItemText.bind(this);
    }

    render() {
        return (
            <div>
                {this.state.message ? this.state.message : ''}
                <ul>
                    {
                        this.state.items.map((item, i) => {
                            return (
                                <DataItem
                                    key={i}
                                    number={i}
                                    value={item.name}
                                    onChange={this.updateItem}
                                    onChangeText={this.updateItemText}
                                />
                            );
                        })
                    }
                </ul>
                <button onClick={this.add}>Добавить</button>
                <button onClick={this.save}>Сохранить</button>
            </div>
        );
    }

    add() {
        let items = this.state.items;
        items.push({
            name: 'h1',
            value: ''
        });

        this.setState({message: null, items: items});
    }

    save() {
        fetch(
            '/save',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify({
                    items: this.state.items
                })
            }
        ).then(r => r.json()).then(r => {
            this.setState({
                message: r.id,
                items: []
            })
        });
    }

    updateItem(k, v) {
        let items = this.state.items;
        items[k].name = v;

        this.setState({items: items});
    }

    updateItemText(k, v) {
        let items = this.state.items;
        items[k].value = v;

        this.setState({items: items});
    }
}


const domContainer = document.querySelector('#app');
ReactDOM.render(React.createElement(DataList), domContainer);
