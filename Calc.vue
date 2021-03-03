<template>
  <div class="container p-3">
    <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
      <ol class="breadcrumb">
        <router-link to="/portfolio" class="breadcrumb-item">
          <a href="#">Портфолио</a>
        </router-link>

        <li class="breadcrumb-item active" aria-current="page">Калькулятор</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col col-md-8 col-lg-6 m-auto bg-secondary p-2 rounded">
        <div class="cal text-center">
          <div
            class="wrap_del d-flex justify-content-between align-items-center"
          >
            <span class="flex"
              >Разрядность вводимого аргумента: {{ countLetters }} (max =
              12)</span
            >
            <button @click="delLastLetter" class="flex btn btn-success">
              &#8592;
            </button>
          </div>
          <div class="display mt-1 shadow p-3 mb-2 bg-secondary rounded">
            <input
              @keypress.prevent
              type="text"
              id="display"
              class="text-right"
              :class="[lessFont ? 'fs' : '']"
              v-bind:value="strDisplay"
            />
          </div>

          <div
            @click="getstrDisplay"
            class="calc-bth d-flex flex-wrap justify-content-center"
            id="calc-btn"
          >
            <button
              v-for="item in btns"
              v-bind:key="item"
              class="btn"
              :class="[
                Number.isInteger(item)
                  ? 'btn-info'
                  : item == 'C'
                  ? 'btn-danger'
                  : item == '='
                  ? 'btn-primary'
                  : 'btn-warning',
              ]"
              :value="item"
            >
              {{ item }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Calc",
  data() {
    return {
      btns: [7, 8, 9, "+", 4, 5, 6, "-", 1, 2, 3, "*", "C", 0, "=", "/"],
      strDisplay: "",
      signs: ["+", "-", "*", "/"],
      lastLetterIsNumber: true,
      lastLetter: "",
      isMoreOneSign: false,
      signForResult: "",
      countLetters: 0,
      isEInStr: -1,
      lessFont: false,
    };
  },
  mounted() {
    this.strDisplay = "0";
  },

  watch: {
    strDisplay: function () {
      if (this.strDisplay.length > 15) {
        this.lessFont = true;
      } else {
        this.lessFont = false;
      }
      this.lastLetter = this.lastSymbol(this.strDisplay);
      this.lastLetterIsNumber = this.ch_isNumeric(this.lastLetter);

      this.isEInStr = this.findEinStr(this.strDisplay);

      if (this.is_signMoreOne(this.strDisplay)) {
        var strForArray = this.changeStr(this.strDisplay);

        var arr = this.makeArrArguments(strForArray);

        var result = this.getResult(this.signForResult, arr);

        this.strDisplay = result + (this.lastLetter ? this.lastLetter : "");
      }
    },
  },

  methods: {
    getstrDisplay(e) {
      if (e.target.tagName !== "BUTTON") {
        return;
      }

      var num = e.target.value;

      switch (num) {
        case "C":
          this.strDisplay = "0";
          this.countLetters = 0;
          break;
        case "=":
          this.coreCalculator(this.strDisplay);
          this.countLetters = 0;
          break;
        default:
          this.renderStr(num);
          break;
      }
    },

    renderStr(num) {
      //обнуляет счетчик цифр(лимит для разрядности числа) при нажатии на знак
      //чтобы можно было ввести второй аргумент если первый уже достиг лимита разрядности
      if (!this.ch_isNumeric(num)) {
        this.countLetters = 0;
      }
      if (this.ch_isNumeric(num) && this.countLetters < 12) {
        //введено значение цифра и счетчик разряда числа не достиг лимита

        if (this.strDisplay !== "0") {
          this.strDisplay += num;
        } else {
          this.strDisplay = num;
        }
        this.countLetters++;
        return;
      } else if (this.lastLetterIsNumber && !this.ch_isNumeric(num)) {
        //последний символ до этого числа  цифра и сейчас введен знак
        this.strDisplay += num;
      } else if (!this.ch_isNumeric(num)) {
        // последний символ до этого числа знак потому сейчас пришел знак надо его поменять на тот что был
        this.strDisplay =
          this.strDisplay.substring(0, this.strDisplay.length - 1) + num;
      }
    },

    is_signMoreOne(str) {
      //в случаях когда в строке есть число вида 1е+20
      if (this.isEInStr != -1) {
        //сдвиг каретки в строке на позицию после е+
        //чтобы правильно определить наличие двух знаков в строке
        //для начала производимых вычислений
        var d = str.substring(this.isEInStr + 2, str.length);
      } else {
        var d = str.substring(1, str.length);
      }

      d = d.replace(/[0-9.1e]/g, "");

      if (d.length > 1) {
        return true;
      } else {
        return false;
      }
    },
    //проверка на число введенное значение
    ch_isNumeric(num) {
      return Number.isInteger(num * 1);
    },
    lastSymbol(str) {
      return str.slice(-1);
    },
    //получает два аргумента из строки дисплея для применения над ними арифмитического действия
    makeArrArguments(str) {
      var arrArgs = str.split(",");

      arrArgs.forEach((element, index) => {
        element * 1;
        if (element == NaN) {
          arrArgs.splice(index, 1);
        }
      });

      return arrArgs;
    },
    //механизм произведения самих вычислений над аргументами
    getResult(sign, arr) {
      switch (sign) {
        case "+":
          return arr[0] * 1 + arr[1] * 1;
        case "-":
          return arr[0] - arr[1];
        case "*":
          return arr[0] * arr[1];
        case "/":
          // для случая деления на 0
          if (arr[1] == 0) {
            return 0;
          }
          return arr[0] / arr[1];
      }
    },
    changeStr(str, lim = 1) {
      var t = str.substring(0, str.length - lim);

      var sign = t.replace(/[0-9.1e]/g, "");

      //для случаев когда нет знаков : 9 =
      //т.е. при нажатии на  = без двух аргументов на дисплее
      if (sign.length == 0) {
        sign = "+";
      }
      if (sign.length > 1) {
        sign = sign.substring(1, sign.length);
      }

      this.signForResult = sign;
      var pos = t.lastIndexOf(sign);

      if (pos == -1) {
        var strNew = t + ",0";
      } else {
        var strNew = t.substr(0, pos) + "," + t.substr(pos + 1);
      }

      return strNew;
    },
    coreCalculator(str) {
      var strForArray = this.changeStr(this.strDisplay, 0);

      var arr = this.makeArrArguments(strForArray);

      var result = this.getResult(this.signForResult, arr);

      this.strDisplay = result + "";
    },

    delLastLetter() {
      if (
        this.strDisplay.length > 1 &&
        this.lastLetterIsNumber &&
        this.countLetters > 0
      ) {
        this.strDisplay = this.strDisplay.substring(
          0,
          this.strDisplay.length - 1
        );
        this.countLetters--;
      }
    },
    findEinStr(str) {
      var e = str.lastIndexOf("e");
      return e;
    },
  },
};
</script>

<style scoped>
#display {
  width: 95%;
  height: 3rem;
  font-size: 2.5rem;
}

button {
  width: 23%;
  height: 4rem;
  margin: 0.08rem;
  font-size: 2rem !important;
}

.wrap_del {
  text-align: right;
}
.calc-wrap {
  min-width: 300px;
}
input {
  text-align: right;
}
.fs {
  font-size: 1.9rem !important;
}
</style>