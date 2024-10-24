var apiUrl = 'http://localhost:3000/';

$(function () {

    if ($("#addNewUser").length !== 0) {
        fetchRequest('GET', 'users/form').then(data => {

            if (data.type === 'error') {
                alert('Error: ' + data.msg);
                return;
            }

            $.each(data.msg, function (key, curValue) {
                $("#addNewUser form").append($('<input>', {
                    type: curValue.type,
                    id: key,
                    name: key,
                    placeholder: key,
                    validations: curValue.validations.join('|')
                }));
            });

            $("#addNewUser form").append($('<div>', {
                class: 'errors'
            }));
            $("#addNewUser form").append($('<input>', {
                type: 'submit',
                value: 'submit'
            }));
        });
    }

    if ($("#usersList").length !== 0) {
        fetchRequest('GET', 'users').then(data => {

            if (data.type === 'error') {
                alert('Error: ' + data.msg);
                return;
            }

            $.each(data.msg, function (key, values) {
                const backgroundColor = getRandomColor();
                const color = invertColor(backgroundColor);
                const tr = `
                <tr style="background-color:${backgroundColor}; color:${color};">
                    <td class="getUserData" data-username="${values.username}">${values.username}</td>
                    <td>${values.email}</td>
                    <td><button class="js-delete" data-username="${values.username}">delete</button></td>
                </tr>
                `;

                $("#usersList table tbody").append(tr);
            });

            $("#usersList table tbody tr").hover(function () {
                const color = $(this).css('color');
                $(this).css('color', $(this).css('background-color'));
                $(this).css('background-color', color);
            });

            $(".js-delete").on('click', function (e) {
                if (confirm('Are you sure?')) {
                    fetchRequest('DELETE', 'users/' + $(this).data('username')).then(data => {

                        if (data.type === 'success') {
                            $(this).parents('tr').remove();
                        }

                        if (data.type === 'error') {
                            alert('Error: ' + data.msg);
                        }

                    });
                }
            });

            $(".getUserData").on('click', function (e) {
                fetchRequest('GET', 'users/' + $(this).data('username')).then(data => {
                    if (data.type === 'error') {
                        alert('Error: ' + data.msg);
                        return;
                    }

                    $.each(data.msg, function (key, curValue) {
                        $(".popup.userData .data").append($('<p>', {
                            html: `${key}: ${curValue}`
                        }));
                    });
                    popup();
                });
            });

        });
    }



    $(".onSubmitForm").on('submit', function (e) {
        e.preventDefault();

        const v = new Validation($(this).find('input'));
        const errors = v.validate();

        if (errors.length) {
            const divs = errors.map(item => `<div>${item}</div>`).join('');
            $('.errors').show().html(divs);

            return;
        }

        fetchRequest('POST', 'users', this).then(data => {
            alert(`${data.type}: ${data.msg}`);
        });

    });
});

function popup(popup) {
    if ($("#overlay").is(":visible")) {
        $("#overlay").hide();
        $(".popup .data").html('');
    } else {
        $("#overlay").css('display', 'flex');
    }
}

function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function invertColor(color) {
    color = color.substring(1); // remove #
    color = parseInt(color, 16); // convert to integer
    color = 0xFFFFFF ^ color; // invert three bytes
    color = color.toString(16); // convert to hex
    color = ("000000" + color).slice(-6); // pad with leading zeros
    color = "#" + color; // prepend #
    return color;
}

async function fetchRequest(method, action, formData) {
    const options = {
        method: method,
    };

    const myHeaders = new Headers();
    myHeaders.append('Content-Type', 'application/json');
    options.headers = myHeaders;

    const data = {};

    if (formData !== undefined) {
        (new FormData(formData)).forEach((value, key) => {
            data[key] = value;
        });

        options.body = JSON.stringify(data);
    }

    return await fetch(apiUrl + action, options)
        .then(response => response.json())
        .then(resData => {
            return resData;
        })
        .catch(error => console.error(error))
}



class Validation {
    constructor(form) {
        this.form = form;
    }

    validate() {
        this.errors = [];

        $.each(this.form, (index, el) => {
            const validations = $(el).attr('validations');
            const name = $(el).attr('name');
            const value = $(el).val();

            if (validations !== undefined) {
                validations.split('|').forEach((vl) => {
                    var [method, limit] = vl.split(',');

                    if (typeof this[method] === 'function') {
                        this[method](name, value, limit);
                    }
                });
            }
        })

        return this.errors;

    }

    required(name, value) {
        if (value === '')
            this.errors.push(`${name} is required`)
    }

    email(name, value) {
        if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value))
            this.errors.push(`${name} is not valid`)
    }

    lengthMin(name, value, min) {
        if (value.length < min)
            this.errors.push(`${name} must be at least ${min} characters long`)
    }

    lengthMax(name, value, max) {
        if (value.length > max)
            this.errors.push(`${name} must be maximum ${max} characters long`)
    }

    alphaOnly(name, value) {
        if (!/^[a-zA-Z]+$/.test(value))
            this.errors.push(`${name} must contain letters only`)
    }

    numericOnly(name, value) {
        return /^[0-9]+$/.test(value);
    }

    url(name, value) {
        if (!/^(https?:\/\/)[^\s$.?#].[^\s]*$/.test(value))
            this.errors.push(`${name} is not valid`)
    }

    lowercase(name, value, min) {
        const matches = value.match(/[a-z]/g);
        if (!matches || matches.length < min)
            this.errors.push(`${name} must contain at least ${min} lowercase`)
    }

    uppercase(name, value, min) {
        const matches = value.match(/[A-Z]/g);
        if (!matches || matches.length < min)
            this.errors.push(`${name} must contain at least ${min} uppercase`)
    }

    special(name, value, min) {
        const matches = value.match(/[!@#$%^&*(),.?":{}|<>]/g);
        if (!matches || matches.length < min) {
            this.errors.push(`${name} must contain at least ${min} special chars`)
        }
    }

    date(name, value) {
        const date = new Date(value);
        if (isNaN(date.getTime()))
            this.errors.push(`${name} has an invalid date`);
    }
}